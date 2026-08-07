<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categories = App\Models\Category::all();
echo "Total: " . $categories->count() . "\n";
foreach($categories->take(5) as $c) {
    echo "ID: $c->id, Name: $c->name, Status: $c->status\n";
}
