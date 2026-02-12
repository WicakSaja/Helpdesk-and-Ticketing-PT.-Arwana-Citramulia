<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;

class NewTicketNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Jumlah percobaan pengiriman jika gagal (misal koneksi lambat/timeout)
     */
    public int $tries = 5;

    /**
     * Interval (detik) antar percobaan: 
     */
    public array $backoff = [10, 30, 60, 120, 300];

    public Ticket $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appName = Config::get('app.name', 'Helpdesk Ticketing System');
        $config = Config::get('ticketnotification');

        $ticketDetailUrl = rtrim($config['frontend_url'], '/')
            . '/' . ltrim($config['ticket_detail_path'], '/')
            . '/' . $this->ticket->id;

        $this->ticket->loadMissing(['requester', 'category', 'status']);

        return (new MailMessage)
            ->subject('Ticket Baru: ' . $this->ticket->ticket_number . ' - ' . $this->ticket->subject)
            ->view('emails.new-ticket', [
                'appName' => $appName,
                'helpdeskName' => $notifiable->name,
                'ticket' => $this->ticket,
                'ticketDetailUrl' => $ticketDetailUrl,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_number' => $this->ticket->ticket_number,
            'subject' => $this->ticket->subject,
        ];
    }
}
