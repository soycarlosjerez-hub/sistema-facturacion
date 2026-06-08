<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mesa_categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color', 7)->default('#6b7280');
            $table->string('icono')->nullable();
            $table->integer('orden')->default(0);
            $table->timestamps();
        });

        Schema::table('mesas', function (Blueprint $table) {
            $table->foreignId('categoria_id')->nullable()->constrained('mesa_categorias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
        Schema::dropIfExists('mesa_categorias');
    }
};
