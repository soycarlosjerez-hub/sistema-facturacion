<?php
try {
    $c = new App\Http\Controllers\DashboardDeliveryController();
    $r = $c->dashboard(new Illuminate\Http\Request());
    $html = $r->getContent();
    echo 'OK ' . strlen($html) . ' bytes rendered' . PHP_EOL;
    echo (strpos($html, 'Dashboard Delivery') !== false ? 'Header OK' : 'MISSING header') . PHP_EOL;
} catch (\Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
    echo $e->getFile() . ':' . $e->getLine() . PHP_EOL;
}