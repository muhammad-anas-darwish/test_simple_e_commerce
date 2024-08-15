<?php

namespace App\Rules;

use App\Models\Product;
use App\Services\ProductService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AvailableQuantity implements ValidationRule
{
    protected ProductService $productService;

    public function __construct(protected $productId, )
    {
        $this->productService = new ProductService();
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!$this->productService->isQuantityAvailable($this->productId, $value)) {
            $fail('The requested quantity is not available in stock'); 
        }
    }
}
