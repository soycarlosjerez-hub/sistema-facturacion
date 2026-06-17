<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\BusinessType;

$bt = BusinessType::where('slug','restaurante')->first();
if ($bt) {
    echo "BusinessType found:\n";
    var_export($bt->toArray());
    echo "\nModules via relationship:\n";
    $mods = $bt->modules()->get();
    foreach ($mods as $m) {
        echo $m->module_key.' visible='.$m->visible.' orden='.$m->orden."\n";
    }
} else {
    echo "No BusinessType with slug 'restaurante' found.\n";
}
?>
