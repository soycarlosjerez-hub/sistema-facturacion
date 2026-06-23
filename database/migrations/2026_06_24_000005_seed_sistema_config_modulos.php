<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();
        $modulos = [
            // Sistema
            ['key' => 'auditoria', 'label' => 'Auditoría', 'icon' => 'bi-journal-text', 'categoria' => 'sistema', 'orden' => 40, 'activo' => true],
            ['key' => 'backups', 'label' => 'Backups', 'icon' => 'bi-cloud-arrow-down', 'categoria' => 'sistema', 'orden' => 41, 'activo' => true],
            // Configuración
            ['key' => 'ncf', 'label' => 'Comprobantes (NCF)', 'icon' => 'bi-receipt-cutoff', 'categoria' => 'configuracion', 'orden' => 50, 'activo' => true],
            ['key' => 'ecf', 'label' => 'e-CF (DGII)', 'icon' => 'bi-shield-check', 'categoria' => 'configuracion', 'orden' => 51, 'activo' => true],
            ['key' => 'secuencias-ecf', 'label' => 'Secuencias e-CF', 'icon' => 'bi-hash', 'categoria' => 'configuracion', 'orden' => 52, 'activo' => true],
            ['key' => 'certificados-digitales', 'label' => 'Certificados Digitales', 'icon' => 'bi-key', 'categoria' => 'configuracion', 'orden' => 53, 'activo' => true],
            ['key' => 'payment-processors', 'label' => 'Procesadores de Pago', 'icon' => 'bi-credit-card', 'categoria' => 'configuracion', 'orden' => 54, 'activo' => true],
            ['key' => 'delivery-companies', 'label' => 'Delivery Companies', 'icon' => 'bi-truck', 'categoria' => 'configuracion', 'orden' => 55, 'activo' => true],
            ['key' => 'impresoras', 'label' => 'Impresoras', 'icon' => 'bi-printer', 'categoria' => 'configuracion', 'orden' => 56, 'activo' => true],
            ['key' => 'configuracion-general', 'label' => 'Parámetros', 'icon' => 'bi-sliders', 'categoria' => 'configuracion', 'orden' => 57, 'activo' => true],
        ];

        foreach ($modulos as $m) {
            DB::table('modulos')->updateOrInsert(
                ['key' => $m['key']],
                array_merge($m, ['created_at' => $now, 'updated_at' => $now])
            );
        }
    }

    public function down(): void
    {
        DB::table('modulos')->whereIn('key', [
            'auditoria', 'backups',
            'ncf', 'ecf', 'secuencias-ecf', 'certificados-digitales',
            'payment-processors', 'delivery-companies', 'impresoras',
            'configuracion-general',
        ])->delete();
    }
};
