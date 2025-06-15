<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserBookingConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $from;
    public $to;
    public Booking $booking;
    /**
     * Create a new notification instance.
     */
    public function __construct($from, $to, Booking $booking)
    {
        $this->from = $from;
        $this->to = $to;
        $this->booking = $booking;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Booking Confirmed')
            ->line("Your booking #{$this->booking->id} has been confirmed.")
            ->line("Date: {$this->booking->booking_date}")
            ->line("Time: {$this->booking->start_time} to {$this->booking->end_time}")
            ->line("Provider:{$this->booking->provider->name}")
            ->line("Service: {$this->booking->service->name}")
            ->line("Price: {$this->booking->price}");
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'message' => 'Booking confirmed!',
            'from' => $this->from->name,
            'to' => $this->to->name,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
