<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'business_type_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('business_type_id')->nullable()->constrained('business_types')->onDelete('set null')->after('role');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'business_type_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropConstrainedForeignId('business_type_id');
            });
        }
    }
};