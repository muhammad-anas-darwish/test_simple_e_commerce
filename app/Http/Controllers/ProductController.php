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

    /**
     * The model class used for the ProductController.
     *
     * @var string
     */
    protected $modelClass = Product::class; 

    /**
     * ProductController constructor.
     */
    public function __construct()
    {
        $this->resourceClass = ProductResource::class;
        parent::__construct(); 
    }

    /**
     * Define the request class for storing a product.
     *
     * @return string The fully qualified class name of the store request.
     */
    protected function storeRequest(): string
    {
        return StoreProductRequest::class;
    }
    
    /**
     * Define the request class for updating a product.
     *
     * @return string The fully qualified class name of the update request.
     */
    protected function updateRequest(): string
    {
        return UpdateProductRequest::class;
    }
}
