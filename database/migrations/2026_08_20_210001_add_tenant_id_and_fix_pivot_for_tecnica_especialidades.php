<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tecnica_especialidades', function (Blueprint $table) {
            $table->unsignedBigInteger('tenant_id')->nullable()->index();
            $table->unsignedInteger('orden')->default(0);
        });

        $defaultTenant = DB::table('business_instances')->orderBy('id')->value('id');
        DB::table('tecnica_especialidades')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenant]);

        if (Schema::hasTable('tecnico_tecnica_especialidad') && !Schema::hasTable('tecnica_especialidad_tecnico')) {
            Schema::rename('tecnico_tecnica_especialidad', 'tecnica_especialidad_tecnico');
        }

        Schema::table('tecnica_especialidad_tecnico', function (Blueprint $table) {
            $table->timestamp('fecha_asignacion')->nullable();
            $table->string('nivel_experiencia', 50)->nullable();
            $table->boolean('activo')->default(true);
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('tecnica_especialidad_tecnico')) {
            Schema::table('tecnica_especialidad_tecnico', function (Blueprint $table) {
                foreach (['fecha_asignacion', 'nivel_experiencia', 'activo'] as $col) {
                    if (Schema::hasColumn('tecnica_especialidad_tecnico', $col)) {
                        $table->dropColumn($col);
                    }
                }
            });

            if (!Schema::hasTable('tecnico_tecnica_especialidad')) {
                Schema::rename('tecnica_especialidad_tecnico', 'tecnico_tecnica_especialidad');
            }
        }

        if (Schema::hasTable('tecnica_especialidades')) {
            Schema::table('tecnica_especialidades', function (Blueprint $table) {
                if (Schema::hasIndex('tecnica_especialidades', ['tenant_id'])) {
                    $table->dropIndex(['tenant_id']);
                }
                if (Schema::hasColumn('tecnica_especialidades', 'tenant_id')) {
                    $table->dropColumn('tenant_id');
                }
                if (Schema::hasColumn('tecnica_especialidades', 'orden')) {
                    $table->dropColumn('orden');
                }
            });
        }
    }
};