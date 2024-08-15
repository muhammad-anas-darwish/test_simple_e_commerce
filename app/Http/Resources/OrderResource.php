<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_item' => OrderItemResource::collection($this->whenLoaded('orderItems')),
            'user' => new UserResource($this->whenLoaded('user')),
            'status' => $this->status,
            'total' => '$' . number_format($this->total, 2),
            'address' => $this->address,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
