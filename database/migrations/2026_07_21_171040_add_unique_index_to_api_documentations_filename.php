<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('api_documentations', function (Blueprint $table) {
            $table->string('filename', 191)->change();
        });

        $indexes = DB::select("SHOW INDEX FROM api_documentations WHERE Key_name = 'api_documentations_filename_unique'");
        if (empty($indexes)) {
            Schema::table('api_documentations', function (Blueprint $table) {
                $table->unique('filename');
            });
        }
    }

    public function down(): void
    {
        Schema::table('api_documentations', function (Blueprint $table) {
            $table->dropUnique(['filename']);
            $table->string('filename', 255)->change();
        });
    }
};
