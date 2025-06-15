<?php

namespace App\Listeners;

use App\Events\BookingCancelled;
use App\Notifications\ProviderBookingCancelledNotification;
use App\Notifications\UserBookingCancelledNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingCancelledNotifications
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BookingCancelled $event): void
    {
        $event->booking->user->notify(new UserBookingCancelledNotification(config('mail.from.address'),$event->booking->user->name, $event->booking));
        $event->booking->provider->notify(new ProviderBookingCancelledNotification(config('mail.from.address'),$event->booking->provider->name, $event->booking));
    }
}
