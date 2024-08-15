<?php

namespace App\Services;

use App\Models\Product;

class ProductService 
{
    public function isQuantityAvailable($productId, $quantity)
    {
        $product = Product::findOrFail($productId);
        if ($product && $quantity <= $product->quantity) { // quantity of products bigger than or equal needed quantity
            return true;
        }
        return false;
    }
}