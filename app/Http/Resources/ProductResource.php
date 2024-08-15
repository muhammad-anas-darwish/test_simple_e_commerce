<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isCollection = $this->resource instanceof \Illuminate\Http\Resources\Json\AnonymousResourceCollection;
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $isCollection ? substr($this->description, 0, 50) . '...' : $this->description,
            'price' => '$' . number_format($this->price, 2),
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
