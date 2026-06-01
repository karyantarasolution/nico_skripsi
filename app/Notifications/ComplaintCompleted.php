<?php

namespace App\Notifications;

use App\Models\MaintenanceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintCompleted extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceOrder $order)
    {
    }

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Perbaikan Selesai! Laporan #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Keluhan Anda telah selesai diperbaiki oleh teknisi.')
            ->line('Judul: ' . $this->order->complaint_title)
            ->line('Silakan cek hasil perbaikan dan berikan penilaian Anda.')
            ->action('Lihat & Beri Penilaian', url('/complaints/' . $this->order->id))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "*Perbaikan Selesai! ✅*\n\n"
            . "Halo {$notifiable->name},\n\n"
            . "Keluhan Anda telah selesai diperbaiki.\n"
            . "Judul: {$this->order->complaint_title}\n\n"
            . "Silakan cek dan beri penilaian:\n"
            . url('/complaints/' . $this->order->id) . "\n\n"
            . "Terima kasih.";
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Perbaikan Selesai',
            'message' => "Laporan #{$this->order->id}: {$this->order->complaint_title} telah selesai",
            'order_id' => $this->order->id,
            'url' => url('/complaints/' . $this->order->id),
            'icon' => 'fas fa-check-circle',
            'color' => 'green',
        ];
    }
}
