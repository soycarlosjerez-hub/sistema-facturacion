<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Get the business type ID for "restaurante"
        $typeId = DB::table('business_types')->where('slug', 'restaurante')->value('id');
        if (! $typeId) {
            return; // nothing to do if the type does not exist
        }

        // Ensure the module key "restaurante" exists and is visible
        $exists = DB::table('business_type_modules')
            ->where('business_type_id', $typeId)
            ->where('modulo_key', 'restaurante')
            ->exists();

        if (! $exists) {
            DB::table('business_type_modules')->insert([
                'business_type_id' => $typeId,
                'modulo_key' => 'restaurante',
                'visible' => true,
                'orden' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            // Make sure it is visible in case it was hidden
            DB::table('business_type_modules')
                ->where('business_type_id', $typeId)
                ->where('modulo_key', 'restaurante')
                ->update([
                    'visible' => true,
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        // Optionally remove the module entry (but keep data safe)
        $typeId = DB::table('business_types')->where('slug', 'restaurante')->value('id');
        if ($typeId) {
            DB::table('business_type_modules')
                ->where('business_type_id', $typeId)
                ->where('modulo_key', 'restaurante')
                ->delete();
        }
    }
};
