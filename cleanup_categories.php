<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

DB::table('categorizables')->truncate();
DB::table('categories')->truncate();

echo "Truncated categories and categorizables tables\n";