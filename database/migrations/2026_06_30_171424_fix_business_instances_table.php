<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('business_instances', function (Blueprint $table) {
            if (!Schema::hasColumn('business_instances', 'slug')) {
                $table->string('slug')->unique()->after('nombre');
            }
            if (!Schema::hasColumn('business_instances', 'rnc')) {
                $table->string('rnc')->nullable()->unique()->after('slug');
            }
            if (!Schema::hasColumn('business_instances', 'email')) {
                $table->string('email')->nullable()->after('rnc');
            }
            if (!Schema::hasColumn('business_instances', 'telefono')) {
                $table->string('telefono')->nullable()->after('email');
            }
            if (!Schema::hasColumn('business_instances', 'direccion')) {
                $table->string('direccion')->nullable()->after('telefono');
            }
            if (!Schema::hasColumn('business_instances', 'business_type_id')) {
                $table->foreignId('business_type_id')->nullable()->constrained('business_types')->onDelete('restrict')->after('direccion');
            }
            if (!Schema::hasColumn('business_instances', 'owner_user_id')) {
                $table->foreignId('owner_user_id')->nullable()->constrained('users')->onDelete('set null')->after('business_type_id');
            }
            if (!Schema::hasColumn('business_instances', 'activo')) {
                $table->boolean('activo')->default(true)->after('owner_user_id');
            }
            if (!Schema::hasColumn('business_instances', 'fecha_vencimiento')) {
                $table->timestamp('fecha_vencimiento')->nullable()->after('activo');
            }
            if (!Schema::hasColumn('business_instances', 'costo_mensual')) {
                $table->decimal('costo_mensual', 10, 2)->nullable()->default(0)->after('fecha_vencimiento');
            }
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->foreign('tenant_id')->references('id')->on('business_instances')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
        });

        Schema::table('business_instances', function (Blueprint $table) {
            $cols = ['slug', 'rnc', 'email', 'telefono', 'direccion', 'business_type_id', 'owner_user_id', 'activo', 'fecha_vencimiento', 'costo_mensual'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('business_instances', $col)) {
                    if (in_array($col, ['business_type_id', 'owner_user_id'])) {
                        $table->dropConstrainedForeignId($col);
                    } else {
                        $table->dropColumn($col);
                    }
                }
            }
        });
    }
};