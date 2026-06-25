<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extract unique ubicaciones from existing mesas and create records
        $ubicaciones = DB::table('mesas')
            ->whereNotNull('ubicacion')
            ->where('ubicacion', '!=', '')
            ->distinct()
            ->pluck('ubicacion');

        $ubicacionMap = [];
        foreach ($ubicaciones as $nombre) {
            $id = DB::table('mesa_ubicaciones')->insertGetId([
                'nombre' => $nombre,
                'activa' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $ubicacionMap[$nombre] = $id;
        }

        // Add ubicacion_id column
        Schema::table('mesas', function (Blueprint $table) {
            $table->foreignId('ubicacion_id')->nullable()->after('capacidad')
                  ->constrained('mesa_ubicaciones')->nullOnDelete();
        });

        // Migrate existing data
        if (!empty($ubicacionMap)) {
            foreach ($ubicacionMap as $nombre => $id) {
                DB::table('mesas')
                    ->where('ubicacion', $nombre)
                    ->update(['ubicacion_id' => $id]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('mesas', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_id']);
            $table->dropColumn('ubicacion_id');
        });
    }
};
