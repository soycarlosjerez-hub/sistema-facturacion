# Fix: Backups de 121 bytes (09/09/2026)

## Problema detectado

Los backups `backup_facturacion_db_20260909_193459.sql`, `193516.sql`, `193600.sql` son de 121 bytes y contienen:

```
mysqldump: Got error: 1045: Access denied for user 'factura_db'@'localhost' (using password: YES) when trying to connect
```

### Causa raíz (2 problemas)

1. **MySQL conecta por localhost (socket) vs app conecta por 127.0.0.1 (TCP)** - El usuario `factura_db` está creado para `127.0.0.1` pero `mysqldump` intenta `localhost`.

2. **Validación insuficiente en BackupRun.php** - El comando usa `exec()` con redirección `2>&1` que hace que PHP retorne código 0 aunque `mysqldump` falle. El error se escribe en el archivo pero se marca como "completado".

## Soluciones

### Fix 1: BackupRun.php → usar BackupService

**Archivo**: `app/Console/Commands/BackupRun.php`

Reemplazar TODO el archivo con:

```php
<?php

namespace App\Console\Commands;

use App\Services\BackupService;
use Illuminate\Console\Command;

class BackupRun extends Command
{
    protected $signature = 'backup:run {--type=automatico : manual o automatico}';
    protected $description = 'Realiza backup de la base de datos';

    protected BackupService $backupService;

    public function __construct(BackupService $backupService)
    {
        parent::__construct();
        $this->backupService = $backupService;
    }

    public function handle(): int
    {
        $type = $this->option('type');
        $this->info("Iniciando backup tipo: {$type}...");

        try {
            $backup = $this->backupService->createBackup($type, null, false);

            if ($backup->status === 'completado') {
                $this->info("Backup completado: {$backup->filename} (" . $backup->sizeForHumans() . ")");

                if ($type === 'automatico') {
                    $cleaned = \App\Models\Backup::cleanOldBackups();
                    if ($cleaned > 0) {
                        $this->info("Backups antiguos limpiados: {$cleaned}");
                    }
                }

                return Command::SUCCESS;
            } else {
                $this->error("Backup fallido: {$backup->notes}");
                return Command::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error("Excepción al crear backup: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
```

### Fix 2: BackupService.php → forzar conexión TCP 127.0.0.1

**Archivo**: `app/Services/BackupService.php`, línea 45

Cambiar:
```php
$dbHost = config('database.connections.mysql.host', '127.0.0.1');
```

Por:
```php
$dbHost = '127.0.0.1'; // Forzar TCP para evitar confusión localhost vs 127.0.0.1
```

Y línea 62 - El archivo temporal de MySQL necesita agregar `protocol=TCP`:
```php
file_put_contents($tmpCnf, "[client]\nhost=\"{$dbHost}\"\nprotocol=TCP\nuser=\"{$dbUser}\"\npassword=\"{$dbPass}\"\n");
```

### Fix 3: BackupService.php → validar contenido SQL generado

**Archivo**: `app/Services/BackupService.php`, reemplazar la validación después de línea 84:

Cambiando:
```php
if ($returnVar !== 0 || !file_exists($fullPath) || filesize($fullPath) === 0) {
```

Por:
```php
if ($returnVar !== 0 || !file_exists($fullPath) || filesize($fullPath) < 100) {
    // Validar contenido del SQL
    $contentValido = false;
    if (file_exists($fullPath)) {
        $handle = fopen($fullPath, 'r');
        $header = fread($handle, 2048);
        fclose($handle);
        $contentValido = preg_match('/(CREATE |LOCK TABLES |UNLOCK TABLES |INSERT INTO |DROP DATABASE)/i', $header);
    }
    
    if (!$contentValido) {
        $errorMsg = file_get_contents($fullPath) ?: 'Archivo vacío o sin contenido SQL válido';
        Log::error('BackupService: Backup falló', ['error' => $errorMsg, 'cmd' => $cmd]);
        
        return $this->createBackupRecord([
            'filename' => $filename,
            'filepath' => $relativePath,
            'size_bytes' => 0,
            'type' => $type,
            'status' => 'fallido',
            'notes' => $errorMsg,
            'user_id' => Auth::id(),
        ]);
    }
}
```

### Fix 4: Crear usuario MySQL localhost

Ejecutar en MySQL:
```sql
CREATE USER 'factura_db'@'localhost' IDENTIFIED BY '67e4?aRkRrth';
GRANT ALL PRIVILEGES ON facturacion_db.* TO 'factura_db'@'localhost';
FLUSH PRIVILEGES;
```

O si el usuario ya existe solo para `127.0.0.1`:
```sql
CREATE USER 'factura_db'@'localhost' IDENTIFIED BY '67e4?aRkRrth';
GRANT ALL PRIVILEGES ON facturacion_db.* TO 'factura_db'@'localhost';
FLUSH PRIVILEGES;
```

### Fix 5: Eliminar backups corruptos y regenerar

```bash
# Eliminar los 3 backups corruptos de 121 bytes
rm storage/app/backups/backup_facturacion_db_20260909_193459.sql
rm storage/app/backups/backup_facturacion_db_20260909_193516.sql
rm storage/app/backups/backup_facturacion_db_20260909_193600.sql

# Eliminar registros en la BD de backups fallidos
php artisan tinker
```

Dentro de tinker:
```php
\App\Models\Backup::where('size_bytes', 0)->delete();
exit
```

Después de aplicar todos los fixes:
```bash
php artisan backup:run --type=manual
```

Verificar que el backup se cree correctamente:
```bash
ls -lh storage/app/backups/ | tail -3
```

## Verificación final

1. Correr `php artisan backup:run --type=manual` y verificar output exitoso
2. Verificar `ls -lh storage/app/backups/` - archivo debería ser >100MB
3. Verificar DB: `SELECT * FROM backups ORDER BY created_at DESC LIMIT 3;` - todos `completado`
4. Esperar al siguiente backup automático (00:00) para confirmar que funciona

## Resumen de cambios

| Archivo | Cambio |
|---------|--------|
| `app/Console/Commands/BackupRun.php` | Reemplazar con BackupService, eliminar código duplicado de mysqldump |
| `app/Services/BackupService.php` | Forzar TCP (127.0.0.1), validar contenido SQL |
| MySQL | Crear usuario 'factura_db'@'localhost' |
| `storage/app/backups/` | Eliminar 3 archivos corruptos de 121 bytes |
| `backups` (tabla) | Eliminar registros con size_bytes=0 del 09/09 |
