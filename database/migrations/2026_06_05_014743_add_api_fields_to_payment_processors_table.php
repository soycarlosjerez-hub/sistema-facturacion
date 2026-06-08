<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payment_processors', function (Blueprint $table) {
            $table->string('api_key', 255)->nullable()->after('comision_fija');
            $table->text('api_secret')->nullable()->after('api_key');
            $table->string('api_endpoint', 500)->nullable()->after('api_secret');
            $table->string('api_environment', 20)->default('sandbox')->after('api_endpoint');
            $table->json('config_json')->nullable()->after('api_environment');
        });
    }

    public function down(): void
    {
        Schema::table('payment_processors', function (Blueprint $table) {
            $table->dropColumn(['api_key', 'api_secret', 'api_endpoint', 'api_environment', 'config_json']);
        });
    }
};
