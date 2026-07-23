<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'marca')) {
                $table->string('marca')->nullable()->after('descripcion');
            }
            if (!Schema::hasColumn('productos', 'modelo')) {
                $table->string('modelo')->nullable()->after('marca');
            }
            if (!Schema::hasColumn('productos', 'capacidad_toneladas')) {
                $table->decimal('capacidad_toneladas', 5, 2)->nullable()->after('modelo');
            }
            if (!Schema::hasColumn('productos', 'capacidad_btu')) {
                $table->unsignedInteger('capacidad_btu')->nullable()->after('capacidad_toneladas');
            }
            if (!Schema::hasColumn('productos', 'tipo_equipo')) {
                $table->string('tipo_equipo')->nullable()->after('capacidad_btu');
            }
            if (!Schema::hasColumn('productos', 'eficiencia_seer')) {
                $table->decimal('eficiencia_seer', 4, 1)->nullable()->after('tipo_equipo');
            }
            if (!Schema::hasColumn('productos', 'gas_refrigerante')) {
                $table->string('gas_refrigerante')->nullable()->after('eficiencia_seer');
            }
            if (!Schema::hasColumn('productos', 'voltaje')) {
                $table->string('voltaje')->nullable()->after('gas_refrigerante');
            }
            if (!Schema::hasColumn('productos', 'peso_kg')) {
                $table->decimal('peso_kg', 8, 2)->nullable()->after('voltaje');
            }
            if (!Schema::hasColumn('productos', 'dimensiones')) {
                $table->string('dimensiones')->nullable()->after('peso_kg');
            }
            if (!Schema::hasColumn('productos', 'categoria_clima')) {
                $table->string('categoria_clima')->nullable()->after('dimensiones');
            }
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
