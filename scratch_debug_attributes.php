<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$product = Product::first();
if (!$product) {
    echo "No product found.\n";
    exit;
}

echo "Model Attributes: " . json_encode(array_keys($product->getAttributes())) . "\n";
echo "Fillable: " . json_encode($product->getFillable()) . "\n";

// In Spatie, we call attributesToBeLogged()
// Since it's protected, we might need reflection or just look at the code.
// Actually, it's public in the trait usually, let's check.

if (method_exists($product, 'attributesToBeLogged')) {
    echo "Attributes to be logged: " . json_encode($product->attributesToBeLogged()) . "\n";
} else {
    echo "attributesToBeLogged method not found on model.\n";
}

$options = $product->getActivitylogOptions();
echo "LogOptions logAttributes: " . json_encode($options->logAttributes) . "\n";
echo "LogOptions logFillable: " . ($options->logFillable ? 'true' : 'false') . "\n";
echo "LogOptions logOnlyDirty: " . ($options->logOnlyDirty ? 'true' : 'false') . "\n";
