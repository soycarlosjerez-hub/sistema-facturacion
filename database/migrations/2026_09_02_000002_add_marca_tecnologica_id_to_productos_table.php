<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Agrega la columna marca_tecnologica_id a la tabla productos
     * para relacionar productos con marcas tecnológicas catalogadas.
     */
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'marca_tecnologica_id')) {
                $table->unsignedBigInteger('marca_tecnologica_id')->nullable()->after('marca');
                $table->foreign('marca_tecnologica_id')
                    ->references('id')
                    ->on('marca_tecnologicas')
                    ->nullOnDelete()
                    ->restrictOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['marca_tecnologica_id']);
            $table->dropColumn('marca_tecnologica_id');
        });
    }
};
