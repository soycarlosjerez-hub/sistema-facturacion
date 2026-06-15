<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    DB::statement('ALTER TABLE business_types ADD UNIQUE KEY `business_types_key_unique` (`key`)');
    echo "Unique constraint added\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

// Set defaults for color_default and icono_default
DB::statement("UPDATE business_types SET icono_default = icon WHERE icono_default IS NULL");
DB::statement("UPDATE business_types SET color_default = CASE 
    WHEN color = 'primary' THEN '#3b82f6'
    WHEN color = 'secondary' THEN '#6b7280'
    WHEN color = 'success' THEN '#22c55e'
    WHEN color = 'info' THEN '#06b6d4'
    WHEN color = 'warning' THEN '#f59e0b'
    WHEN color = 'danger' THEN '#ef4444'
    ELSE '#3b82f6'
END WHERE color_default IS NULL");

echo "Defaults set\n";

$results = DB::table('business_types')->get(['id', 'key', 'slug', 'nombre', 'color_default', 'icono_default']);
foreach ($results as $r) {
    echo "{$r->id}: key='{$r->key}', color_default='{$r->color_default}', icono_default='{$r->icono_default}'\n";
}