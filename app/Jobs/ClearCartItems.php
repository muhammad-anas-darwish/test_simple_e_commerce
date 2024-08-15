<?php

namespace App\Jobs;

use App\Models\CartItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearCartItems implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected $userId)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // remove all cart items for user
        CartItem::where('user_id', $this->userId)->delete();
    }
}
