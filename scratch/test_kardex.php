<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Exports\KardexExport;
use App\Http\Controllers\KardexController;
use Illuminate\Http\Request;

echo "Calling KardexController@index with search 'test'...\n";

try {
    $request = Request::create('/kardex', 'GET', ['buscar' => 'test']);
    
    // Simulate user login since there is an auth check or tenant check
    $user = \App\Models\User::first();
    if ($user) {
        auth()->login($user);
        echo "Logged in as: " . $user->email . "\n";
    }

    $controller = new KardexController();
    $response = $controller->index($request);
    echo "Controller call succeeded! View returned.\n";

    echo "Testing KardexExport collection method...\n";
    $export = new KardexExport(null, null, 'test');
    $collection = $export->collection();
    echo "KardexExport collection() succeeded! Count: " . $collection->count() . "\n";
    
    if ($collection->count() > 0) {
        echo "Mapping first row...\n";
        $row = $export->map($collection->first());
        print_r($row);
    }
} catch (\Exception $e) {
    echo "Failed with exception: " . $e->getMessage() . "\n";
}
