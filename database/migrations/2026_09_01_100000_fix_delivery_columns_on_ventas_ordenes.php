<?php

use App\Support\SafeAlterTable;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla VENTAS: agregar columnas faltantes
        if (! SafeAlterTable::hasColumn('ventas', 'delivery_zone_id')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('delivery_company_id');
                $table->index('delivery_zone_id');
            });
        }

        if (! SafeAlterTable::hasColumn('ventas', 'driver_id')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('delivery_zone_id');
                $table->index('driver_id');
            });
        }

        if (! SafeAlterTable::hasColumn('ventas', 'distancia_km')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->decimal('distancia_km', 8, 2)->nullable()->after('driver_id');
            });
        }

        if (! SafeAlterTable::hasColumn('ventas', 'tarifa_delivery')) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->decimal('tarifa_delivery', 10, 2)->nullable()->after('distancia_km');
            });
        }

        // Tabla ORDENES: agregar columnas faltantes
        if (! SafeAlterTable::hasColumn('ordenes', 'driver_id')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('entrega_empresa_id');
                $table->index('driver_id');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'tracking_status')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('tracking_status', 30)->default('pendiente')->after('driver_id');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'fecha_entrega_estimada')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dateTime('fecha_entrega_estimada')->nullable()->after('tracking_status');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'fecha_entrega_real')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dateTime('fecha_entrega_real')->nullable()->after('fecha_entrega_estimada');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'prueba_entrega_foto')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('prueba_entrega_foto', 500)->nullable()->after('fecha_entrega_real');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'prueba_entrega_firma')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('prueba_entrega_firma', 500)->nullable()->after('prueba_entrega_foto');
            });
        }

        if (! SafeAlterTable::hasColumn('ordenes', 'notas_entrega')) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->text('notas_entrega')->nullable()->after('prueba_entrega_firma');
            });
        }

        // Tabla conduces: agregar sucursal_id si no existe
        if (! SafeAlterTable::hasColumn('conduces', 'sucursal_id')) {
            Schema::table('conduces', function (Blueprint $table) {
                $table->unsignedBigInteger('sucursal_id')->nullable()->after('user_id');
                $table->index('sucursal_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropColumn(['delivery_zone_id', 'driver_id', 'distancia_km', 'tarifa_delivery']);
        });

        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropColumn([
                'driver_id', 'tracking_status', 'fecha_entrega_estimada',
                'fecha_entrega_real', 'prueba_entrega_foto', 'prueba_entrega_firma',
                'notas_entrega',
            ]);
        });

        Schema::table('conduces', function (Blueprint $table) {
            $table->dropColumn('sucursal_id');
        });
    }
};
