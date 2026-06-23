<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::whereNotNull('business_instance_id')->first();
if ($user && $user->business_instance_id) {
    \DB::table('productos')->whereNull('tenant_id')->update(['tenant_id' => $user->business_instance_id]);
    \DB::table('compras')->whereNull('tenant_id')->update(['tenant_id' => $user->business_instance_id]);
    echo "Fixed empty tenant_ids using user instance " . $user->business_instance_id . PHP_EOL;
} else {
    echo "No valid user instance found" . PHP_EOL;
}
