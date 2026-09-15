<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->foreignId('driver_id')
                ->nullable()
                ->after('entrega_empresa_id')
                ->constrained('delivery_drivers')
                ->nullOnDelete();
            $table->string('tracking_status', 30)
                ->default('pendiente')
                ->after('driver_id');
            $table->dateTime('fecha_entrega_estimada')
                ->nullable()
                ->after('tracking_status');
            $table->dateTime('fecha_entrega_real')
                ->nullable()
                ->after('fecha_entrega_estimada');
            $table->string('prueba_entrega_foto', 500)
                ->nullable()
                ->after('fecha_entrega_real');
            $table->string('prueba_entrega_firma', 500)
                ->nullable()
                ->after('prueba_entrega_foto');
            $table->text('notas_entrega')
                ->nullable()
                ->after('prueba_entrega_firma');
        });
    }

    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign(['driver_id']);
            $table->dropColumn([
                'driver_id',
                'tracking_status',
                'fecha_entrega_estimada',
                'fecha_entrega_real',
                'prueba_entrega_foto',
                'prueba_entrega_firma',
                'notas_entrega',
            ]);
        });
    }
};
