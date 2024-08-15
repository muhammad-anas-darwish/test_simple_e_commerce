<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Traits\ApiResponses;

class ProductController extends BaseController
{
    use ApiResponses;

    protected $modelClass = Product::class; 

    public function __construct()
    {
        $this->resourceClass = ProductResource::class;
        parent::__construct(); 
    }
    protected function storeRequest(): string
    {
        return StoreProductRequest::class;
    }
    
    protected function updateRequest(): string
    {
        return UpdateProductRequest::class;
    }
}
