<?php

namespace App\Listeners;

use App\Events\BookingConfirmed;
use App\Notifications\ProviderBookingConfirmedNotification;
use App\Notifications\UserBookingConfirmedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendBookingConfirmationNotifications
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
    public function handle(BookingConfirmed $event): void
    {
        $event->booking->user->notify(new UserBookingConfirmedNotification(config('mail.from.address'),$event->booking->user->name, $event->booking));
        $event->booking->provider->notify(new ProviderBookingConfirmedNotification(config('mail.from.address'),$event->booking->provider->name, $event->booking));
    }
}
