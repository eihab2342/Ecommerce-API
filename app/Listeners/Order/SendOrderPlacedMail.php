<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPlaced;
use App\Mail\Order\OrderPlacedMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;

class SendOrderPlacedMail implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct() {}

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        Mail::to($event->order->email)->queue(new OrderPlacedMail($event->order));
    }
}