<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flowhub_id_map', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('business_instances')->cascadeOnDelete();
            $table->string('flow_id', 36)->unique();
            $table->string('model', 50)->index();
            $table->unsignedBigInteger('real_id');
            $table->timestamps();

            $table->index(['model', 'real_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flowhub_id_map');
    }
};
