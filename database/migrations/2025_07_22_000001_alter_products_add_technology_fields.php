<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->enum('especializacion', ['celular', 'accesorio', 'domotica', 'servicio', 'pieza'])
                ->default('accesorio')
                ->after('categoria_id');

            $table->boolean('vendible_imei')->default(false)->after('especializacion');
            $table->boolean('requiere_imei')->default(false)->after('vendible_imei');
            $table->string('marca', 100)->nullable()->after('requiere_imei');
            $table->string('modelo', 200)->nullable()->after('marca');
            $table->string('almacenamiento_gb', 20)->nullable()->after('modelo');
            $table->string('color', 50)->nullable()->after('almacenamiento_gb');
            $table->decimal('precio_servicio', 10, 2)->default(0)->after('color');
            $table->unsignedInteger('duracion_servicio_horas')->default(0)->after('precio_servicio');
            $table->unsignedInteger('garantia_dias')->default(30)->after('duracion_servicio_horas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'especializacion',
                'vendible_imei',
                'requiere_imei',
                'marca',
                'modelo',
                'almacenamiento_gb',
                'color',
                'precio_servicio',
                'duracion_servicio_horas',
                'garantia_dias',
            ]);
        });
    }
};
