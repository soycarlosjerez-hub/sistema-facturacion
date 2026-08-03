<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // sale_created, sale_cancelled, payment_received, shift_opened, shift_closed, purchase_created, low_stock, ncf_expiring
            $table->string('category')->default('system'); // sale, order, payment, inventory, cash, fiscal, system
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable(); // icon, color, action_url, category_icon, category_label
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'category']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notifications');
    }
};
