<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$maxBatch = DB::table('migrations')->max('batch') ?: 1;

$tables = [
    'evaluaciones_proveedores' => '2026_08_20_105000_create_evaluaciones_proveedores_table',
];

foreach ($tables as $table => $migrationName) {
    if (Schema::hasTable($table)) {
        $existing = DB::table('migrations')->where('migration', $migrationName)->first();
        if (!$existing) {
            DB::table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $maxBatch + 1,
            ]);
            echo "Marked as run: $migrationName\n";
        } else {
            echo "Already marked: $migrationName\n";
        }
    } else {
        echo "Table not found: $table\n";
    }
}

echo "Max batch was: $maxBatch\n";
