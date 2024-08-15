<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Exceptions\CustomJsonException;
use App\Jobs\ClearCartItems;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;

class OrderService
{
    public function getPaginatedOrders($countPerPage = 10): LengthAwarePaginator
    {
        return Order::paginate($countPerPage);
    }

    public function getPaginatedUserOrders($userId, $countPerPage = 10): LengthAwarePaginator
    {
        return Order::with('orderItems.product')->where('user_id', $userId)->paginate($countPerPage);
    }

    private function getCartItems($userId): Builder
    {
        $cartItems = CartItem::with('product')->where('user_id', $userId);
        if (!$cartItems->exists()) {
            throw new CustomJsonException('Cart is empty', 400);
        }
        return $cartItems;
    }

    private function updateOrderTotal($order, $total): void
    {
        $order['total'] = $total;
        $order->save();
    }

    private function convertChatItemsToOrderItems($order, Builder $cartItems)
    {
        $total = 0;
        $cartItems->each(function ($cartItem) use ($order, &$total) {
            OrderItem::create([
                'product_id' => $cartItem->product_id,
                'order_id' => $order->id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem['product']['price'],
            ]);
            $total += $cartItem->quantity * $cartItem['product']['price'];
        });
        return $total;
    }

    private function createOrder($userId, $address): Order
    {
        return Order::create([
            'user_id' => $userId,
            'total' => 0,
            'status' => OrderStatus::Pending->value,
            'address' => $address,
        ]);
    }

    public function checkout($userId, $address): Order
    {
        try {
            DB::beginTransaction(); // Starting the transaction

            $cartItems = $this->getCartItems($userId);
            $order = $this->createOrder($userId, $address);
            $total = $this->convertChatItemsToOrderItems($order, $cartItems);
            $this->updateOrderTotal($order, $total);
            Queue::push(new ClearCartItems($userId)); // remove all items in tha cart for user

            DB::commit(); // Commit the changes
            return $order;
        }
        catch (\Exception $e) {
            DB::rollBack(); // Rollback in case of an exception
            throw $e;
        }
    }

    public function changeStatus($order, $status): Order
    {
        $order->status = $status;
        $order->save();

        return $order->load('orderItems.product');
    }

    public function showOrder($order)
    {
        return $order->load('orderItems.product');
    }
}
