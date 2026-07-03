<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            if (!Schema::hasColumn('proveedores', 'activo')) {
                $table->boolean('activo')->default(true)->after('email');
                $table->index('activo');
            }
        });

        // Set existing records as active
        DB::table('proveedores')->whereNull('activo')->update(['activo' => true]);
    }

    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropIndex(['activo']);
            $table->dropColumn('activo');
        });
    }
};
