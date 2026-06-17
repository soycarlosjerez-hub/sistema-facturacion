<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessType;

// Fetch BusinessType row for 'restaurante'
$bt = BusinessType::where('slug','restaurante')->first();
echo "\nBusinessType row:\n";
var_export($bt ? $bt->toArray() : null);

// Cached collection
$all = BusinessType::allCached();
echo "\n\nCached entry for 'restaurante':\n";
var_export($all['restaurante'] ?? null);

// Visible modules via relationship (no cache)
$modules = $bt ? $bt->modules()->where('visible',true)->orderBy('orden')->pluck('modulo_key')->toArray() : [];
echo "\n\nVisible modules (raw query):\n";
var_export($modules);

echo "\nDone.\n";
?>
