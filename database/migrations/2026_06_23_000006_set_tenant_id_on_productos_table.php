<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Set tenant_id to a default tenant (e.g., 1) for existing products where null
        DB::table('productos')->whereNull('tenant_id')->update(['tenant_id' => 1]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally set tenant_id back to null for records that were set to 1
        DB::table('productos')->where('tenant_id', 1)->update(['tenant_id' => null]);
    }
};
?>
