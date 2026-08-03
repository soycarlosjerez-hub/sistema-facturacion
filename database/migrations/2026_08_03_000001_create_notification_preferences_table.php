<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('business_instance_id')->nullable()->constrained()->onDelete('cascade');
            
            // Ventas y ordenes
            $table->boolean('sale_created')->default(true);
            $table->boolean('sale_paid')->default(true);
            $table->boolean('sale_cancelled')->default(true);
            $table->boolean('order_confirmed')->default(true);
            $table->boolean('order_ready')->default(true);
            $table->boolean('order_shipped')->default(true);
            
            // Cobros y pagos
            $table->boolean('payment_received')->default(true);
            $table->boolean('credit_overdue')->default(true);
            $table->boolean('credit_abaono')->default(true);
            
            // Inventario
            $table->boolean('stock_critical')->default(true);
            $table->boolean('stock_restocked')->default(false);
            $table->boolean('product_created')->default(false);
            
            // Caja y turnos
            $table->boolean('shift_opened')->default(true);
            $table->boolean('shift_closed')->default(true);
            $table->boolean('cash_shortage')->default(true);
            $table->boolean('daily_report')->default(false);
            
            // Fiscal
            $table->boolean('ncff_expiring')->default(true);
            $table->boolean('ecf_certificate_expiring')->default(true);
            
            // Sistema
            $table->boolean('backup_completed')->default(false);
            $table->boolean('backup_failed')->default(true);
            $table->boolean('user_registered')->default(true);
            
            $table->timestamps();
            $table->unique(['user_id', 'business_instance_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
    }
};
