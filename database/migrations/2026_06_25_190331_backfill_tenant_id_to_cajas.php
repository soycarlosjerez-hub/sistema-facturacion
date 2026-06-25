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
        // Assign tenant_id to cajas that have null tenant_id
        // Uses the first business instance as default
        DB::table('cajas')
            ->whereNull('tenant_id')
            ->update(['tenant_id' => DB::table('business_instances')->orderBy('id')->value('id')]);
    }

    public function down(): void
    {
        // Cannot reverse — previous null values are lost
    }
};
