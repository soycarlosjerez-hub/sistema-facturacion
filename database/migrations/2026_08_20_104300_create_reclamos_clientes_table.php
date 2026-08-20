<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('reclamos_clientes')) {
            Schema::create('reclamos_clientes', function (Blueprint $table) {
                $table->id();
                $table->string('codigo', 30)->unique();
                $table->unsignedBigInteger('cliente_id');
                $table->string('canal', 50)->default('web');
                $table->string('tipo', 50)->default('reclamo');
                $table->text('descripcion');
                $table->string('estado', 20)->default('abierto');
                $table->date('fecha_resolucion')->nullable();
                $table->text('resolucion')->nullable();
                $table->integer('tiempo_respuesta_horas')->nullable();
                $table->integer('satisfaccion_resolucion')->nullable()->comment('1-5');
                $table->unsignedBigInteger('encuesta_satisfaccion_id')->nullable();
                $table->unsignedBigInteger('asignado_a')->nullable();
                $table->unsignedBigInteger('creado_por')->nullable();
                $table->unsignedBigInteger('tenant_id')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('asignado_a')->references('id')->on('users')->onDelete('set null');
                $table->foreign('creado_por')->references('id')->on('users')->onDelete('set null');
            });
        } else {
            // La tabla ya existe pero le faltaba la FK
            if (!Schema::hasColumn('reclamos_clientes', 'encuesta_satisfaccion_id')) {
                Schema::table('reclamos_clientes', function (Blueprint $table) {
                    $table->unsignedBigInteger('encuesta_satisfaccion_id')->nullable()->after('satisfaccion_resolucion');
                });
            }

            try {
                Schema::table('reclamos_clientes', function (Blueprint $table) {
                    $table->foreign('encuesta_satisfaccion_id')->references('id')->on('encuestas_satisfaccion')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // FK ya existe, continuar
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reclamos_clientes');
    }
};
