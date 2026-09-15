<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;

class OwnerSettingsController extends Controller
{
    /**
     * Variables CRÍTICAS que no se pueden editar desde la UI
     */
    private const CRIITICAL_VARS = [
        'APP_KEY',
        'DB_PASSWORD',
        'DB_HOST',
        'DB_PORT',
        'DB_USERNAME',
        'DB_DATABASE',
        'DGII_API_KEY',
        'DGII_API_KEY_SANDBOX',
        'DGII_API_KEY_QA',
        'DGII_API_KEY_PROD',
        'DGII_CERT_CLIENT_KEY_PASS_SANDBOX',
        'DGII_CERT_CLIENT_KEY_PASS_QA',
        'DGII_CERT_CLIENT_KEY_PASS_PROD',
        'AI_API_KEY',
        'MAIL_PASSWORD',
    ];

    /**
     * Definición de grupos con sus variables y descripciones
     */
    public function getGroups(): array
    {
        return [
            'app' => [
                'label' => 'Aplicación',
                'icon' => 'bi-gear',
                'vars' => [
                    'APP_NAME' => ['label' => 'Nombre del Sistema', 'description' => 'Nombre que aparece en la UI y correos'],
                    'APP_URL' => ['label' => 'URL Base del Sistema', 'description' => 'Dominio principal (https://...)'],
                    'APP_LOCALE' => ['label' => 'Idioma Predeterminado', 'description' => 'es, en, etc.'],
                    'APP_FALLBACK_LOCALE' => ['label' => 'Idioma de Respaldo', 'description' => 'Idioma fallback si no hay traducción'],
                    'APP_DEBUG' => ['label' => 'Modo Debug', 'description' => 'true = muestra errores (solo local), false = producción'],
                    'ITBIS_DEFAULT' => ['label' => 'ITBIS Predeterminado', 'description' => 'Porcentaje de ITBIS (18%)'],
                    'MONEDA_DEFAULT' => ['label' => 'Moneda Predeterminada', 'description' => 'RD$, USD, EUR, etc.'],
                ],
            ],
            'database' => [
                'label' => 'Base de Datos',
                'icon' => 'bi-database',
                'vars' => [
                    'DB_CONNECTION' => ['label' => 'Conexión', 'description' => 'mysql, pgsql, sqlite'],
                ],
            ],
            'cache' => [
                'label' => 'Caché',
                'icon' => 'bi-memory',
                'vars' => [
                    'CACHE_STORE' => ['label' => 'Driver de Caché', 'description' => 'file, database, redis'],
                    'SESSION_DRIVER' => ['label' => 'Driver de Sesiones', 'description' => 'file, database, redis'],
                ],
            ],
            'mail' => [
                'label' => 'Correo (SMTP)',
                'icon' => 'bi-envelope',
                'vars' => [
                    'MAIL_MAILER' => ['label' => 'Mailer', 'description' => 'smtp, log, mailgun, sendmail'],
                    'MAIL_HOST' => ['label' => 'Host SMTP', 'description' => 'Servidor SMTP'],
                    'MAIL_PORT' => ['label' => 'Puerto SMTP', 'description' => '587, 465, 25...'],
                    'MAIL_USERNAME' => ['label' => 'Usuario SMTP', 'description' => 'Email de envío'],
                    'MAIL_ENCRYPTION' => ['label' => 'Encriptación', 'description' => 'tls, ssl, null'],
                    'MAIL_FROM_ADDRESS' => ['label' => 'From Email', 'description' => 'Email remitente'],
                    'MAIL_FROM_NAME' => ['label' => 'From Nombre', 'description' => 'Nombre remitente'],
                ],
            ],
            'dgii' => [
                'label' => 'DGII / Facturación',
                'icon' => 'bi-receipt',
                'vars' => [
                    'DGII_AMBIENTE' => ['label' => 'Ambiente DGII', 'description' => 'sandbox, qa, produccion'],
                    'DGII_SIMULAR' => ['label' => 'Modo Simulación', 'description' => 'true = no envía a DGII real'],
                    'DGII_PROB_APROB' => ['label' => 'Probabilidad Aprobación', 'description' => '0-1, prob simulada de aprobación'],
                ],
            ],
            'security' => [
                'label' => 'Seguridad',
                'icon' => 'bi-shield-lock',
                'vars' => [
                    'BCRYPT_ROUNDS' => ['label' => 'BCrypt Rounds', 'description' => 'Costo de hashing (12 es recomendado)'],
                    'SESSION_SECURE_COOKIE' => ['label' => 'Secure Cookie', 'description' => 'true = solo HTTPS'],
                    'SESSION_LIFETIME' => ['label' => 'Sesiones (min)', 'description' => 'Duración de sesión en minutos'],
                    'MULTITENANCY_ENABLED' => ['label' => 'Multi-Tenancy', 'description' => 'true = sistema multi-tenant activado'],
                ],
            ],
            'backup' => [
                'label' => 'Backups',
                'icon' => 'bi-arrow-repeat',
                'vars' => [
                    'BACKUP_AUTO_SCHEDULE' => ['label' => 'Auto Backup', 'description' => 'true = backups automáticos programados'],
                    'BACKUP_ENCRYPT' => ['label' => 'Encryptar Backup', 'description' => 'true = archivos backup cifrados'],
                    'BACKUP_RETENTION_DAYS' => ['label' => 'Días Retención', 'description' => 'Cuántos días mantener backups'],
                    'BACKUP_COMPRESS' => ['label' => 'Comprimir', 'description' => 'true = backups comprimidos'],
                ],
            ],
            'ai' => [
                'label' => 'IA / AI',
                'icon' => 'bi-stars',
                'vars' => [
                    'AI_API_URL' => ['label' => 'AI API URL', 'description' => 'Endpoint del servicio IA'],
                    'AI_MODEL' => ['label' => 'Modelo IA', 'description' => 'Nombre del modelo a usar'],
                    'AI_STREAM' => ['label' => 'Stream', 'description' => 'true = respuesta en streaming'],
                    'AI_MAX_TOKENS' => ['label' => 'Max Tokens', 'description' => 'Máximo de tokens por respuesta'],
                    'AI_DISABLE_THINKING' => ['label' => 'Sin Thinking', 'description' => 'true = desactiva thinking tokens'],
                ],
            ],
            'telescope' => [
                'label' => 'Telescope (Monitoreo)',
                'icon' => 'bi-activity',
                'vars' => [
                    'TELESCOPE_ENABLED' => ['label' => 'Telescope Activado', 'description' => 'true = monitoreo habilitado'],
                    'TELESCOPE_PATH' => ['label' => 'Telescope Path', 'description' => 'Ruta para acceder (telescope)'],
                ],
            ],
        ];
    }

    /**
     * Mostrar página de configuración del Owner
     */
    public function index(Request $request): View
    {
        $groups = $this->getGroups();
        $envData = $this->readEnv();
        $changeLogs = $this->getRecentChanges($request->user());

        return view('owner.settings', [
            'groups' => $groups,
            'envData' => $envData,
            'changeLogs' => $changeLogs,
            'currentEnv' => app()->environment(),
        ]);
    }

    /**
     * Leer todas las variables del .env
     */
    private function readEnv(): array
    {
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);
        $envLines = explode("\n", $envContent);
        $envData = [];

        foreach ($envLines as $line) {
            $line = trim($line);
            if (empty($line) || str_starts_with($line, '#')) continue;
            
            if (strpos($line, '=') !== false) {
                [$key, $value] = explode('=', $line, 2);
                $envData[trim($key)] = trim($value, ' "');
            }
        }

        return $envData;
    }

    /**
     * Guardar cambios en .env
     */
    public function update(Request $request): JsonResponse
    {
        $user = $request->user();

        // Validar permisos
        if (!$user->hasRole(['owner', 'root'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $validator = Validator::make($request->all(), [
            'changes' => 'required|array',
            'changes.*' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Datos inválidos', 'details' => $validator->errors()], 422);
        }

        $changes = $request->input('changes', []);
        $envPath = base_path('.env');
        $originalContent = file_get_contents($envPath);
        $backupPath = dirname($envPath) . '/.env.backup.' . date('YmdHis');
        
        // Backup automático del .env original
        file_put_contents($backupPath, $originalContent);

        $updated = [];
        $blocked = [];
        $errors = [];

        foreach ($changes as $key => $newValue) {
            // Verificar si es variable crítica
            if (in_array($key, self::CRIITICAL_VARS)) {
                $blocked[] = $key;
                $this->logChange($user, 'blocked', 'critical', $key, null, null, $request);
                continue;
            }

            // Validar nombre de variable (solo letras, números, guiones)
            if (!preg_match('/^[A-Z_][A-Z0-9_]*$/', $key)) {
                $errors[] = "$key: nombre de variable inválido";
                continue;
            }

            // Obtener valor actual
            $currentValue = $this->getEnvValue($key);
            
            if ($currentValue === $newValue) {
                continue; // No cambió
            }

            // Actualizar el .env
            $newContent = preg_replace(
                "/^({$key}=.*)$/m",
                "$key={$newValue}",
                file_get_contents($envPath)
            );

            if ($newContent === file_get_contents($envPath)) {
                $errors[] = "$key: no se pudo actualizar (no encontrada)";
                continue;
            }

            file_put_contents($envPath, $newContent);

            // Re-cargar la variable en el runtime
            putenv("$key=$newValue");
            config([$key => $newValue]);

            // Registrar cambio
            $this->logChange($user, 'updated', 'global', $key, $currentValue, $newValue, $request);
            $updated[] = $key;
        }

        return response()->json([
            'success' => true,
            'updated' => count($updated),
            'updated_vars' => $updated,
            'blocked' => $blocked,
            'errors' => $errors,
            'backup_path' => $backupPath,
        ]);
    }

    /**
     * Obtener valor actual de una variable .env
     */
    private function getEnvValue(string $key): ?string
    {
        // Primero intentar desde Laravel config
        if (config($key) !== null) {
            return (string) config($key);
        }
        
        // Luego desde el archivo directo
        $envData = $this->readEnv();
        return $envData[$key] ?? null;
    }

    /**
     * Registrar cambio en el log
     */
    public function logChange(
        int $userId,
        string $action,
        string $group,
        ?string $variableName = null,
        ?string $oldValue = null,
        ?string $newValue = null
    ): void
    {
        $log = new \App\Models\SystemConfigChangeLog();
        $log->user_id = $userId;
        $log->action = $action;
        $log->group = $group;
        $log->variable_name = $variableName;
        $log->old_value = $oldValue;
        $log->new_value = $newValue;
        $log->save();
    }

    /**
     * Obtener cambios recientes (API)
     */
    public function getChanges(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasRole(['owner', 'root'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $limit = $request->integer('limit', 50);
        $logs = \App\Models\SystemConfigChangeLog::with('user')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json(['changes' => $logs]);
    }

    /**
     * Reiniciar .env a backup más reciente
     */
    public function restoreBackup(Request $request): JsonResponse
    {
        $user = $request->user();
        if (!$user->hasRole(['owner', 'root'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $backupPath = $request->input('backup_path');
        if (empty($backupPath) || !file_exists($backupPath)) {
            return response()->json(['error' => 'Backup no encontrado'], 404);
        }

        // Validar que el backup esté en el directorio correcto
        if (!str_starts_with(realpath($backupPath), base_path('storage'))) {
            return response()->json(['error' => 'Ruta no permitida'], 403);
        }

        $envPath = base_path('.env');
        copy($backupPath, $envPath);
        clearstatcache();

        return response()->json([
            'success' => true,
            'message' => '.env restaurado desde ' . basename($backupPath),
        ]);
    }

    /**
     * Obtener lista de backups disponibles
     */
    public function getBackups(): JsonResponse
    {
        $user = auth()->user();
        if (!$user->hasRole(['owner', 'root'])) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $backupDir = storage_path('app');
        $backups = glob($backupDir . '/.env.backup.*');
        
        $backupList = [];
        foreach ($backups as $b) {
            $backupList[] = [
                'path' => $b,
                'filename' => basename($b),
                'size' => filesize($b),
                'date' => filemtime($b),
            ];
        }

        return response()->json(['backups' => $backupList]);
    }
}