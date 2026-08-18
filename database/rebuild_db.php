<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== DROPPING ALL TABLES ===\n";

// Get all app tables (exclude system tables)
$systemTables = ['migrations', 'password_reset_tokens', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs', 'sessions', 'model_has_permissions', 'model_has_roles', 'role_has_permissions', 'permissions', 'roles'];

try {
    $existing = array_column(DB::select("SHOW TABLES"), 'Tables_in_sistema_facturacion');
    foreach ($existing as $t) {
        if (!in_array($t, $systemTables) && $t !== 'sistema_facturacion') {
            DB::statement('DROP TABLE IF EXISTS `' . $t . '`');
            echo "  Dropped: $t\n";
        }
    }
} catch (\Exception $e) {
    echo "  Error: " . $e->getMessage() . "\n";
}

echo "\n=== CLEARED MIGRATIONS ===\n";
DB::statement('SET FOREIGN_KEY_CHECKS = 0');
DB::table('migrations')->truncate();

echo "\n=== RUNNING MIGRATIONS ===\n";
echo "Running all in batch 9999...\n";

$paths = glob(database_path('migrations') . '*.php');

$completed = [];
try {
    foreach ($paths as $path) {
        $name = basename($path, '.php');
        if ($name === 'rebuild_db') continue;
        
        // Execute the migration file directly
        $migration = include $path;
        $migration->up();
        DB::table('migrations')->insert([
            'migration' => $name,
            'batch' => 9999,
        ]);
        $completed[] = $name;
        echo "  ✓ $name\n";
    }
} catch (\Exception $e) {
    echo "\n✗ Error on: {$name}\n";
    echo "  {$e->getMessage()}\n";
}

// Re-enable FK checks
DB::statement('SET FOREIGN_KEY_CHECKS = 1');

echo "\n=== DONE ===\n";
echo "Run migrations for remaining tables that created via PHP artisan migrate\n";

