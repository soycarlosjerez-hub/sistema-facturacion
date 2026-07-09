<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\SystemSetting;

$settings = SystemSetting::where('key', 'like', 'mail_%')->get();
echo "Total mail settings found: " . $settings->count() . "\n\n";

foreach ($settings as $s) {
    echo $s->key;
    echo ' | tenant_id=' . ($s->tenant_id ?? 'NULL');
    echo ' | value=' . substr((string)$s->value, 0, 40);
    echo "\n";
}

echo "\n--- Business Instances ---\n";
$instancias = \App\Models\BusinessInstance::all();
foreach ($instancias as $i) {
    echo "ID: {$i->id} | Name: {$i->name}\n";
}

$user = \App\Models\User::where('email', 'admin@test.com')->first();
if ($user) {
    echo "\n--- User admin@test.com ---\n";
    echo "business_instance_id: " . ($user->business_instance_id ?? 'NULL') . "\n";
    echo "Roles: " . $user->getRoleNames()->implode(', ') . "\n";
}