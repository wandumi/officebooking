<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BoardroomBookingNotification extends Notification
{
    use Queueable;

    protected array $booking;
    protected string $eventType;


    /**
     * Create a new notification instance.
     */
    public function __construct($booking, $eventType = 'created')
    {
        $this->booking = $booking;
        $this->eventType = $eventType;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $type = ucfirst($this->booking['room_type'] ?? 'Office');
        $statusText = match($this->eventType) {
            'created' => "Your booking for  $type has been created successfully.",
            'approved' => "Your booking for  $type has been approved.",
            'cancelled' => "Your booking for  $type has been cancelled.",
            'rejected' => "Your booking for  $type has been rejected.",
            default => "Booking update.",
        };

        return (new MailMessage)
            ->subject(ucfirst($this->eventType) . ' Booking')
            ->line($statusText)
            ->action('View Booking', url("/boardrooms-booking/{$this->booking['id']}"));


    }

    /**
     * Database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        $type = ucfirst($this->booking['room_type']);
        $statusText = match($this->eventType) {
            'created' => "A new $type has been created.",
            'approved' => "Your $type booking was approved.",
            'cancelled' => "Your $type booking was cancelled.",
            'rejected' => "Your $type booking was rejected.",
            default => "$type booking update.",
        };

        return [
            'title' => ucfirst($this->eventType) . ' Booking',
            'message' => $statusText,
            'booking_id' => $this->booking['id'],
            'room_type' => $this->booking['room_type'],
            'status' => $this->booking['status'],
        ];


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
