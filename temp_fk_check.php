<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Check business_instances referencing business_type_id=7
$count = \DB::table('business_instances')->where('business_type_id', 7)->count();
echo "business_instances con business_type_id=7: " . $count . PHP_EOL;

if ($count > 0) {
    $rows = \DB::table('business_instances')->where('business_type_id', 7)->get();
    foreach ($rows as $row) {
        echo "  ID: " . $row->id . ", Nombre: " . $row->nombre . PHP_EOL;
    }
}

// Show ALL business_type_ids referenced
echo PHP_EOL . "Todos los business_type_id en business_instances:" . PHP_EOL;
$all = \DB::table('business_instances')->select('business_type_id')->distinct()->get();
foreach ($all as $a) {
    echo "  business_type_id: " . $a->business_type_id . PHP_EOL;
}

// Foreign key info
echo PHP_EOL . "Foreign keys sobre business_types:" . PHP_EOL;
$fks = \DB::select("
    SELECT TABLE_NAME, COLUMN_NAME, CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME, UPDATE_RULE, DELETE_RULE
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE REFERENCED_TABLE_NAME = 'business_types' AND TABLE_SCHEMA = DATABASE()
");
foreach ($fks as $fk) {
    echo "  Table: {$fk->TABLE_NAME}, Column: {$fk->COLUMN_NAME}, Rule: {$fk->DELETE_RULE}" . PHP_EOL;
}
