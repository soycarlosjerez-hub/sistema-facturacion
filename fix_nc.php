<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Drop the partially created no_conformidades table
try {
    DB::statement('SET FOREIGN_KEY_CHECKS = 0');
    DB::statement('DROP TABLE IF EXISTS `no_conformidades`');
    DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    echo "Dropped no_conformidades\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Clear the failed migration entry
$deleted = DB::table('migrations')->where('migration', '2026_08_20_108000_create_no_conformidades_table')->delete();
echo "Migration entries cleared: $deleted\n";
