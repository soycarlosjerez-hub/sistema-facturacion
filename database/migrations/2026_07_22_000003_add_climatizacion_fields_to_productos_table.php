<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->string('marca')->nullable()->after('descripcion');
            $table->string('modelo')->nullable()->after('marca');
            $table->decimal('capacidad_toneladas', 5, 2)->nullable()->after('modelo');
            $table->unsignedInteger('capacidad_btu')->nullable()->after('capacidad_toneladas');
            $table->string('tipo_equipo')->nullable()->after('capacidad_btu');
            $table->decimal('eficiencia_seer', 4, 1)->nullable()->after('tipo_equipo');
            $table->string('gas_refrigerante')->nullable()->after('eficiencia_seer');
            $table->string('voltaje')->nullable()->after('gas_refrigerante');
            $table->decimal('peso_kg', 8, 2)->nullable()->after('voltaje');
            $table->string('dimensiones')->nullable()->after('peso_kg');
            $table->string('categoria_clima')->nullable()->after('dimensiones');
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'marca',
                'modelo',
                'capacidad_toneladas',
                'capacidad_btu',
                'tipo_equipo',
                'eficiencia_seer',
                'gas_refrigerante',
                'voltaje',
                'peso_kg',
                'dimensiones',
                'categoria_clima',
            ]);
        });
    }
};
