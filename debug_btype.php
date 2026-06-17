<?php
require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$restaurantId = DB::table('business_types')->where('slug', 'restaurante')->value('id');
if (!$restaurantId) {
    echo "No business_type with slug 'restaurante' found.\n";
    exit;
}

echo "Restaurant ID: $restaurantId\n";
$modules = DB::table('business_type_modules')->where('business_type_id', $restaurantId)->get();
if ($modules->isEmpty()) {
    echo "No rows in business_type_modules for this business type.\n";
} else {
    echo "Modules rows:\n";
    foreach ($modules as $m) {
        echo $m->module_key . ' visible=' . $m->visible . "\n";
    }
}
?>
