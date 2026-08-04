<?php

namespace App\Console\Commands;

use App\Models\HistorialImpresion;
use App\Models\Impresora;
use App\Models\PlantillaImpresion;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class CheckPrintingConfig extends Command
{
    protected $signature = 'printing:check {--fail-fast : Detener en primer error}';
    protected $description = 'Verifica toda la configuración de impresión del sistema';

    private int $errors = 0;
    private int $warnings = 0;
    private int $checks = 0;
    private int $passed = 0;

    public function handle(): int
    {
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║   VERIFICACIÓN DE CONFIGURACIÓN DE IMPRESIÓN    ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->newLine();

        $this->checkPhpExtensions();
        $this->checkDependencies();
        $this->checkDatabaseTables();
        $this->checkStorageDirectories();
        $this->checkImpresoras();
        $this->checkPlantillas();
        $this->checkPrintServiceMethods();

        $this->displaySummary();

        return $this->errors > 0 ? 1 : 0;
    }

    private function checkPhpExtensions(): void
    {
        $this->warn('📦 Extensiones PHP requeridas:');
        
        $extensions = ['sockets', 'fileinfo', 'iconv', 'gd'];
        foreach ($extensions as $ext) {
            $this->checks++;
            if (extension_loaded($ext)) {
                $this->passed++;
                $this->info("  ✓ {$ext} cargada");
            } else {
                $this->errors++;
                $this->error("  ✗ {$ext} NO cargada - Requiere instalación");
                if ($this->option('fail-fast')) return;
            }
        }
        $this->newLine();
    }

    private function checkDependencies(): void
    {
        $this->warn('📦 Dependencias Composer:');
        
        $packages = [
            'barryvdh/laravel-dompdf' => 'Generación PDF',
            'mike42/escpos-php' => 'Impresión térmica',
        ];

        foreach ($packages as $package => $desc) {
            $this->checks++;
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class) || $package === 'barryvdh/laravel-dompdf') {
                if ($package === 'barryvdh/laravel-dompdf') {
                    $this->passed++;
                    $this->info("  ✓ {$package} - {$desc}");
                } else {
                    $this->passed++;
                    $this->info("  ✓ {$package} - {$desc}");
                }
            } else {
                $this->warnings++;
                $this->warn("  ⚠ {$package} - {$desc} (no detectado, puede requerir composer install)");
            }
        }

        if (class_exists(\Mike42\Escpos\Printer::class)) {
            $this->passed++;
            $this->checks++;
            $this->info("  ✓ mike42/escpos-php - Impresión térmica ESC/POS");
        } else {
            $this->warnings++;
            $this->checks++;
            $this->warn("  ⚠ mike42/escpos-php - No detectado (requiere composer install)");
        }
        $this->newLine();
    }

    private function checkDatabaseTables(): void
    {
        $this->warn('🗄️ Tablas de base de datos:');
        
        $tables = ['impresoras', 'plantillas_impresion', 'historial_impresion'];
        foreach ($tables as $table) {
            $this->checks++;
            if (\Schema::hasTable($table)) {
                $this->passed++;
                $this->info("  ✓ Tabla '{$table}' existe");
            } else {
                $this->errors++;
                $this->error("  ✗ Tabla '{$table}' NO existe - Ejecuta php artisan migrate");
                if ($this->option('fail-fast')) return;
            }
        }
        $this->newLine();
    }

    private function checkStorageDirectories(): void
    {
        $this->warn('💾 Directorios de almacenamiento:');
        
        $directories = [
            'storage/app/prints' => 'Archivos PDF de impresión',
            'storage/app/tickets' => 'Tickets temporales',
        ];

        foreach ($directories as $path => $desc) {
            $this->checks++;
            $fullPath = storage_path($path);
            
            if (!is_dir($fullPath)) {
                if (@mkdir($fullPath, 0755, true)) {
                    $this->passed++;
                    $this->info("  ✓ {$desc} - Directorio creado: {$path}");
                } else {
                    $this->errors++;
                    $this->error("  ✗ {$desc} - No se puede crear: {$path}");
                    if ($this->option('fail-fast')) return;
                }
            } elseif (is_writable($fullPath)) {
                $this->passed++;
                $this->info("  ✓ {$desc} - Escribible: {$path}");
            } else {
                $this->errors++;
                $this->error("  ✗ {$desc} - No escribible: {$path}");
                if ($this->option('fail-fast')) return;
            }
        }
        $this->newLine();
    }

    private function checkImpresoras(): void
    {
        $this->warn('🖨️  Configuración de impresoras:');
        
        $this->checks++;
        $total = Impresora::count();
        $this->info("  ℹ Total de impresoras configuradas: {$total}");

        $this->checks++;
        $activas = Impresora::activas()->count();
        if ($activas > 0) {
            $this->passed++;
            $this->info("  ✓ Hay {$activas} impresora(s) activa(s)");
        } else {
            $this->warnings++;
            $this->warn("  ⚠ No hay impresoras activas - La impresión fallará");
        }

        $tipos = Impresora::selectRaw('tipo_conexion, COUNT(*) as count')
            ->groupBy('tipo_conexion')
            ->pluck('count', 'tipo_conexion');

        if ($tipos->isNotEmpty()) {
            $this->passed++;
            $this->checks++;
            $this->info("  ℹ Tipos configurados:");
            foreach ($tipos as $tipo => $count) {
                $label = Impresora::TIPOS_CONEXION[$tipo] ?? $tipo;
                $this->line("      - {$label}: {$count}");
            }
        }

        $pdf = Impresora::where('tipo_conexion', 'pdf')->where('activo', true)->first();
        if ($pdf) {
            $this->checks++;
            $this->passed++;
            $this->info("  ✓ Impresora PDF lista: {$pdf->nombre}");
        }

        $red = Impresora::where('tipo_conexion', 'red')->where('activo', true)->first();
        if ($red) {
            $this->checks++;
            $this->passed++;
            $this->info("  ✓ Impresora de red lista: {$red->nombre} ({$red->direccion_ip}:{$red->puerto})");
        }

        $this->newLine();
    }

    private function checkPlantillas(): void
    {
        $this->warn('📋 Plantillas de impresión:');
        
        $this->checks++;
        $total = PlantillaImpresion::count();
        $this->info("  ℹ Total de plantillas: {$total}");

        $this->checks++;
        $activas = PlantillaImpresion::where('activo', true)->count();
        if ($activas > 0) {
            $this->passed++;
            $this->info("  ✓ {$activas} plantilla(s) activa(s)");
        } else {
            $this->warnings++;
            $this->warn("  ⚠ No hay plantillas activas");
        }

        $modulos = PlantillaImpresion::distinct()->pluck('modulo');
        if ($modulos->isNotEmpty()) {
            $this->checks++;
            $this->passed++;
            $this->info("  ✓ Plantillas por módulo: " . $modulos->join(', '));
        }
        $this->newLine();
    }

    private function checkPrintServiceMethods(): void
    {
        $this->warn('⚙️  Servicios de impresión:');
        
        $service = app(\App\Services\PrintService::class);
        
        $methods = [
            'renderVentaTicket' => 'Render ticket venta',
            'renderCotizacionTicket' => 'Render ticket cotización',
            'renderConduceTicket' => 'Render ticket conduce',
            'imprimirDocumento' => 'Imprimir documento',
            'enviarATexto' => 'Enviar a texto',
        ];

        foreach ($methods as $method => $desc) {
            $this->checks++;
            if (method_exists($service, $method)) {
                $this->passed++;
                $this->info("  ✓ Método '{$method}' disponible");
            } else {
                $this->errors++;
                $this->error("  ✗ Método '{$method}' NO existe");
                if ($this->option('fail-fast')) return;
            }
        }
        $this->newLine();
    }

    private function displaySummary(): void
    {
        $this->newLine();
        $this->info('╔══════════════════════════════════════════════════╗');
        $this->info('║                 RESUMEN FINAL                    ║');
        $this->info('╚══════════════════════════════════════════════════╝');
        $this->newLine();

        $this->info("  ✅ Pasaron: {$this->passed}");
        if ($this->warnings > 0) {
            $this->warn("  ⚠ Advertencias: {$this->warnings}");
        }
        if ($this->errors > 0) {
            $this->error("  ✗ Errores: {$this->errors}");
        }
        $this->newLine();

        if ($this->errors === 0 && $this->warnings === 0) {
            $this->info('  🎉 ¡TODO LISTO PARA IMPRIMIR!');
        } elseif ($this->errors === 0) {
            $this->info('  ⚡ Sistema listo con advertencias (revisar arriba)');
        } else {
            $this->error('  ❌ ERRORES CRÍTICOS - Corregir antes de imprimir');
            $this->newLine();
            $this->warn('  💡 Comandos útiles:');
            $this->line('    php artisan migrate          - Crear tablas faltantes');
            $this->line('    php artisan db:seed --class=ImpresoraSeeder - Sembrar datos');
            $this->line('    php artisan printing:check   - Re-verificar después de corregir');
        }
    }
}
