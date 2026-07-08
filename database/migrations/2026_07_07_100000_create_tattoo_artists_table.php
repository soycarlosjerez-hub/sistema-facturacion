<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tattoo_artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->nullable()->constrained('business_instances')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nombre_completo');
            $table->string('especialidad', 100)->nullable();
            $table->string('foto_perfil')->nullable();
            $table->unsignedTinyInteger('experiencia_anos')->default(0);
            $table->string('telefono', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('instagram', 100)->nullable();
            $table->decimal('comision_pct', 5, 2)->default(30.00);
            $table->text('biografia')->nullable();
            $table->boolean('activo')->default(true);
            $table->string('tipo', 20)->default('empleado');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tattoo_artists');
    }
};
