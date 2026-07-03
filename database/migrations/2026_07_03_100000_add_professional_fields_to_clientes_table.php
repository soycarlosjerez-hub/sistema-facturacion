<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'regimen_mensual')) {
                $table->boolean('regimen_mensual')->default(true)->after('tipo_cliente');
            }
            if (!Schema::hasColumn('clientes', 'nit')) {
                $table->string('nit', 30)->nullable()->after('regimen_mensual');
            }
            if (!Schema::hasColumn('clientes', 'plazo_pago_dias')) {
                $table->integer('plazo_pago_dias')->default(30)->after('balance_pendiente');
            }
            if (!Schema::hasColumn('clientes', 'tasa_descuento_pct')) {
                $table->decimal('tasa_descuento_pct', 5, 2)->default(0)->after('plazo_pago_dias');
            }
            if (!Schema::hasColumn('clientes', 'moneda')) {
                $table->string('moneda', 3)->default('RD')->after('tasa_descuento_pct');
            }
            if (!Schema::hasColumn('clientes', 'auto_bloquear_credito')) {
                $table->boolean('auto_bloquear_credito')->default(true)->after('moneda');
            }
            if (!Schema::hasColumn('clientes', 'notas_internas')) {
                $table->text('notas_internas')->nullable()->after('auto_bloquear_credito');
            }
            if (!Schema::hasColumn('clientes', 'persona_contacto')) {
                $table->string('persona_contacto', 150)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('clientes', 'cargo_contacto')) {
                $table->string('cargo_contacto', 100)->nullable()->after('persona_contacto');
            }
            if (!Schema::hasColumn('clientes', 'whatsapp')) {
                $table->string('whatsapp', 30)->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('clientes', 'ciudad')) {
                $table->string('ciudad', 100)->nullable()->after('direccion');
            }
            if (!Schema::hasColumn('clientes', 'provincia')) {
                $table->string('provincia', 100)->nullable()->after('ciudad');
            }
            if (!Schema::hasColumn('clientes', 'codigo_postal')) {
                $table->string('codigo_postal', 10)->nullable()->after('provincia');
            }
            if (!Schema::hasColumn('clientes', 'segmento')) {
                $table->string('segmento', 20)->default('micro')->after('codigo_postal');
            }
            if (!Schema::hasColumn('clientes', 'origen_cliente')) {
                $table->string('origen_cliente', 30)->default('walkin')->after('segmento');
            }
            if (!Schema::hasColumn('clientes', 'sector_actividad')) {
                $table->string('sector_actividad', 100)->nullable()->after('origen_cliente');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'regimen_mensual', 'nit', 'plazo_pago_dias', 'tasa_descuento_pct',
                'moneda', 'auto_bloquear_credito', 'notas_internas',
                'persona_contacto', 'cargo_contacto', 'whatsapp',
                'ciudad', 'provincia', 'codigo_postal',
                'segmento', 'origen_cliente', 'sector_actividad',
            ]);
        });
    }
};
