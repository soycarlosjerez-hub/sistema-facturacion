<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('email', 'restauranter@restauranter.com')->first();
if ($user) {
    echo "User: " . $user->email . PHP_EOL;
    echo "Business Instance ID: " . ($user->businessInstance ? $user->businessInstance->id : 'NONE') . PHP_EOL;
} else {
    echo "User restauranter@restauranter.com not found." . PHP_EOL;
}
