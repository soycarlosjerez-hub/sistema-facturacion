<?php
require __DIR__.'/bootstrap/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Models\BusinessType;
$bt = BusinessType::where('slug', 'restaurante')->first();
if ($bt) {
    echo "BusinessType ID: {$bt->id}\n";
    $modules = BusinessType::getModulosVisibles('restaurante');
    print_r($modules);
} else {
    echo "No business type found\n";
}
?>
