<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // --- Tabla VENTAS: agregar columnas faltantes ---
        $columns = DB::select("SHOW COLUMNS FROM ventas");
        $columnNames = array_column($columns, 'Field');

        if (!in_array('delivery_zone_id', $columnNames)) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('delivery_zone_id')->nullable()->after('delivery_company_id');
                $table->index('delivery_zone_id');
            });
        }

        if (!in_array('driver_id', $columnNames)) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('delivery_zone_id');
                $table->index('driver_id');
            });
        }

        if (!in_array('distancia_km', $columnNames)) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->decimal('distancia_km', 8, 2)->nullable()->after('driver_id');
            });
        }

        if (!in_array('tarifa_delivery', $columnNames)) {
            Schema::table('ventas', function (Blueprint $table) {
                $table->decimal('tarifa_delivery', 10, 2)->nullable()->after('distancia_km');
            });
        }

        // --- Tabla ORDENES: agregar columnas faltantes ---
        $ordenesColumns = DB::select("SHOW COLUMNS FROM ordenes");
        $ordenNames = array_column($ordenesColumns, 'Field');

        if (!in_array('driver_id', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->unsignedBigInteger('driver_id')->nullable()->after('entrega_empresa_id');
                $table->index('driver_id');
            });
        }

        if (!in_array('tracking_status', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('tracking_status', 30)->default('pendiente')->after('driver_id');
            });
        }

        if (!in_array('fecha_entrega_estimada', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dateTime('fecha_entrega_estimada')->nullable()->after('tracking_status');
            });
        }

        if (!in_array('fecha_entrega_real', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->dateTime('fecha_entrega_real')->nullable()->after('fecha_entrega_estimada');
            });
        }

        if (!in_array('prueba_entrega_foto', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('prueba_entrega_foto', 500)->nullable()->after('fecha_entrega_real');
            });
        }

        if (!in_array('prueba_entrega_firma', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->string('prueba_entrega_firma', 500)->nullable()->after('prueba_entrega_foto');
            });
        }

        if (!in_array('notas_entrega', $ordenNames)) {
            Schema::table('ordenes', function (Blueprint $table) {
                $table->text('notas_entrega')->nullable()->after('prueba_entrega_firma');
            });
        }

        // --- Tabla conduces: agregar sucursal_id si no existe ---
        $conduceColumns = DB::select("SHOW COLUMNS FROM conduces");
        $conduceNames = array_column($conduceColumns, 'Field');

        if (!in_array('sucursal_id', $conduceNames)) {
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
