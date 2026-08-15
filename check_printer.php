<?php
require __DIR__.'/vendor/autoload.php';
$app = new Illuminate\Foundation\Application();
print_r(App\Models\Impresora::all());