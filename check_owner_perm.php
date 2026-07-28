<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Login the owner user
$user = \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'owner'); })->first();
auth()->login($user);

// Clear permission cache
app()['cache']->remove('spatie.permission.cache');

echo "Auth: " . auth()->check() ? 'YES' : 'NO' . "\n";
echo "User: " . auth()->user()->email . "\n";
echo "hasRole(owner): " . (auth()->user()->hasRole('owner') ? 'YES' : 'NO') . "\n";

echo "\nCalling Sidebar::menu()...\n";
$menu = \App\Support\Sidebar::menu();
echo "Menu count: " . count($menu) . "\n";
if (count($menu) > 0) {
    foreach ($menu as $item) {
        if (isset($item['section'])) {
            echo "  [SECTION] {$item['section']}\n";
        } else {
            echo "  - {$item['label']}\n";
        }
    }
} else {
    echo "MENU IS STILL EMPTY!\n";
}
