<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$instances = \App\Models\BusinessInstance::where('business_type_id', 7)->get(['id', 'nombre', 'business_type_id']);
echo "Instancias con business_type_id=7 (tattoo): " . $instances->count() . PHP_EOL;
foreach($instances as $i) {
    echo "  - ID: " . $i->id . ", Nombre: " . $i->nombre . PHP_EOL;
}

// Also check all business types with instances
echo PHP_EOL . "--- Todas las instancias agrupadas por business_type ---" . PHP_EOL;
$groups = \App\Models\BusinessInstance::selectRaw('business_type_id, COUNT(*) as cnt')
    ->groupBy('business_type_id')
    ->having('business_type_id', '>', 0)
    ->get();
foreach($groups as $g) {
    echo "  Type ID {$g->business_type_id}: {$g->cnt} instancia(s)" . PHP_EOL;
}
