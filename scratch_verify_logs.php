<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Activity;

$product = Product::first();
if (!$product) {
    echo "No product found.\n";
    exit;
}

$oldPrice = $product->price;
$product->price = $oldPrice + 1.00;
echo "Attempting to change price from $oldPrice to " . $product->price . "\n";
$product->save();

$activity = Activity::latest()->first();
echo "Activity Description: " . $activity->description . "\n";
echo "Properties captured: " . json_encode($activity->properties, JSON_PRETTY_PRINT) . "\n";
echo "Attribute Changes captured: " . json_encode($activity->attribute_changes, JSON_PRETTY_PRINT) . "\n";
