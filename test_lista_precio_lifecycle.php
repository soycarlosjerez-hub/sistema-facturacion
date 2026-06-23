<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Producto;
use App\Models\ListaPrecio;
use App\Models\ListaPrecioItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

function test_step($name, $callback) {
    echo "Testing: $name... ";
    try {
        $callback();
        echo "✅ SUCCESS" . PHP_EOL;
    } catch (\Exception $e) {
        echo "❌ FAILED" . PHP_EOL;
        echo "   Error: " . $e->getMessage() . PHP_EOL;
        exit(1);
    }
}

// 1. Setup User
$user = User::where('email', 'restauranter@restauranter.com')->first();
if (!$user) {
    die("Error: User not found." . PHP_EOL);
}
Auth::login($user);
echo "Logged in as: {$user->name} ({$user->email})" . PHP_EOL;

// Ensure we have products to work with
$products = Producto::limit(5)->get();
if ($products->isEmpty()) {
    die("Error: No products found in database to test ListaPrecio." . PHP_EOL);
}
$productIds = $products->pluck('id')->toArray();
echo "Using Product IDs: " . implode(', ', $productIds) . PHP_EOL . PHP_EOL;

$listaId = null;
$duplicatedId = null;

// 2. Lifecycle Tests
test_step("Create ListaPrecio", function() use (&$listaId, $productIds) {
    $lista = ListaPrecio::create([
        'codigo' => 'TEST-' . uniqid(),
        'nombre' => 'Lista de Prueba Ciclo',
        'descripcion' => 'Lista para pruebas automatizadas',
        'activa' => true,
    ]);
    $listaId = $lista->id;
    if (!$lista) throw new Exception("Failed to create ListaPrecio");
});

test_step("Add Products to Lista", function() use ($listaId, $productIds) {
    foreach ($productIds as $pid) {
        ListaPrecioItem::create([
            'lista_precio_id' => $listaId,
            'producto_id' => $pid,
            'precio' => 100.00,
        ]);
    }
    $count = ListaPrecioItem::where('lista_precio_id', $listaId)->count();
    if ($count !== count($productIds)) throw new Exception("Expected " . count($productIds) . " items, got $count");
});

test_step("Mass Update Prices (actualizarPrecios)", function() use ($listaId, $productIds) {
    $controller = new \App\Http\Controllers\ListaPrecioController();
    $request = new \Illuminate\Http\Request([
        'precios' => array_map(fn($pid) => ['producto_id' => $pid, 'precio' => 150.00], $productIds)
    ]);
    
    $controller->actualizarPrecios($request, ListaPrecio::find($listaId));
    
    $updatedPrice = ListaPrecioItem::where('lista_precio_id', $listaId)->where('producto_id', $productIds[0])->first()->precio;
    if ((float)$updatedPrice !== 150.00) throw new Exception("Price update failed. Expected 150.00, got $updatedPrice");
});

test_step("Duplicate Lista", function() use (&$duplicatedId, $listaId) {
    $controller = new \App\Http\Controllers\ListaPrecioController();
    $lista = ListaPrecio::find($listaId);
    
    // We simulate the duplication route which redirects
    // Since we are in CLI, we call the method directly
    $controller->duplicar($lista);
    
    // The controller redirects to edit the NEW list. 
    // In a real app, we'd need to find the newly created copy.
    // Let's find the latest copy of this list.
    $copy = ListaPrecio::where('nombre', 'LIKE', $lista->nombre . ' (Copia)%')->latest()->first();
    if (!$copy) throw new Exception("Could not find duplicated list");
    $duplicatedId = $copy->id;
    
    $count = ListaPrecioItem::where('lista_precio_id', $duplicatedId)->count();
    if ($count === 0) throw new Exception("Duplicated list has no items");
});

test_step("Remove Product from Lista", function() use ($listaId, $productIds) {
    $controller = new \App\Http\Controllers\ListaPrecioController();
    $lista = ListaPrecio::find($listaId);
    $item = ListaPrecioItem::where('lista_precio_id', $listaId)->first();
    
    if (!$item) throw new Exception("No item found to remove");
    
    $controller->quitarProducto($lista, $item);
    
    $stillExists = ListaPrecioItem::where('lista_precio_id', $listaId)->where('id', $item->id)->exists();
    if ($stillExists) throw new Exception("Product was not removed from list");
});

test_step("Delete Entire Lista", function() use ($listaId) {
    $controller = new \App\Http\Controllers\ListaPrecioController();
    $lista = ListaPrecio::find($listaId);
    
    $controller->destroy($lista);
    
    if (ListaPrecio::where('id', $listaId)->exists()) throw new Exception("Lista still exists in DB");
    
    $itemsExist = ListaPrecioItem::where('lista_precio_id', $listaId)->exists();
    if ($itemsExist) throw new Exception("Items were not deleted (cascade failed)");
});

echo PHP_EOL . "🎉 ALL TESTS PASSED SUCCESSFULLY!" . PHP_EOL;
