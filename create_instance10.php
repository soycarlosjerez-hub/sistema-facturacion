<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create business type if not exists (Restaurante)
$type = \App\Models\BusinessType::where('nombre', 'Restaurante')->first();
if (!$type) {
    $type = new \App\Models\BusinessType();
    $type->nombre = 'Restaurante';
    $type->slug = 'restaurante';
    $type->save();
    echo "Business type created: " . $type->id . PHP_EOL;
} else {
    echo "Business type exists: " . $type->id . PHP_EOL;
}

// Create instance 10 - but we need to check if ID 10 already exists or if we can create it
// The instances seem to auto-increment from the last one, so let's try to create it

// First, let's see what the max ID is
$maxId = \App\Models\BusinessInstance::max('id');
echo "Max instance ID: " . $maxId . PHP_EOL;

// Try to create instance with specific ID 10
// MySQL auto_increment might not let us insert ID 10 if there are only 3 records
// Let's try inserting with explicit ID

$instance = new \App\Models\BusinessInstance();
$instance->id = 10;  // Try to set ID 10 explicitly
$instance->nombre = 'Gato Negro (Santiago, DR)';
$instance->slug = 'gato-negro-santiagodr-10';
$instance->rnc = 'J-100000000';
$instance->email = 'contacto@gatonegro.do';
$instance->telefono = '+1-809-555-0000';
$instance->direccion = 'Ave. Gregorio Luperon #45, Gurabo, Santiago de los Caballeros';
$instance->business_type_id = $type->id;
$instance->activo = true;
$instance->save();

echo "Instance created: ID " . $instance->id . " - " . $instance->nombre . PHP_EOL;
?>