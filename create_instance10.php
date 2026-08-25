<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create business type first
$type = new \App\Models\BusinessType();
$type->nombre = 'Restaurante';
$type->slug = 'restaurante';
$type->save();

echo 'Business type created: ' . $type->id . PHP_EOL;

// Now create instance 10
$instance = new \App\Models\BusinessInstance();
$instance->nombre = 'Gato Negro (Santiago, DR)';
$instance->slug = 'gato-negro-santiagodr';
$instance->rnc = 'J-300000000';
$instance->email = 'contacto@gatonegro.do';
$instance->telefono = '+1-809-555-0000';
$instance->direccion = 'Ave. Gregorio Luperon #45, Gurabo, Santiago de los Caballeros';
$instance->business_type_id = $type->id;
$instance->activo = true;
$instance->save();

echo 'Instance 10 created successfully!' . PHP_EOL;
?>