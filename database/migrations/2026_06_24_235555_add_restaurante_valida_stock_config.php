<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the main business instance (id=1)
        $instance = DB::table('business_instances')->find(1);
        if (!$instance) {
            return;
        }
        
        // Parse current configuration JSON (null -> empty array)
        $config = json_decode($instance->configuracion ?? '{}', true);
        
        // Add restaurant stock validation feature (default enabled)
        $config['restaurante_valida_stock'] = '1';
        
        // Update the configuration
        DB::table('business_instances')
            ->where('id', 1)
            ->update(['configuracion' => json_encode($config)]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the business instance (id=1)
        $instance = DB::table('business_instances')->find(1);
        if (!$instance) {
            return;
        }
        
        // Parse current configuration JSON
        $config = json_decode($instance->configuracion ?? '{}', true);
        
        // Remove the restaurant stock validation feature
        unset($config['restaurante_valida_stock']);
        
        // Update the configuration
        DB::table('business_instances')
            ->where('id', 1)
            ->update(['configuracion' => json_encode($config)]);
    }
};
