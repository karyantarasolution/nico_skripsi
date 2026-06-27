<?php

namespace App\Notifications;

use App\Models\MaintenanceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TechnicianAssigned extends Notification
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
        $tech = $this->order->technician;

        return (new MailMessage)
            ->subject('Teknisi Ditugaskan untuk Laporan #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Seorang teknisi telah ditugaskan untuk menangani keluhan Anda.')
            ->line('Judul: ' . $this->order->complaint_title)
            ->line('Teknisi: ' . ($tech->name ?? '-'))
            ->line('Spesialisasi: ' . ($tech->specialty ?? '-'))
            ->line('No. Telepon: ' . ($tech->phone ?? '-'))
            ->action('Lihat Detail', url('/complaints/' . $this->order->id))
            ->line('Teknisi akan segera menghubungi Anda.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $tech = $this->order->technician;
        $techName = $tech->name ?? '-';
        $techPhone = $tech->phone ?? '-';
        $techSpecialty = $tech->specialty ?? '-';

        return "*Teknisi Ditugaskan*\n\n"
            . "Halo {$notifiable->name},\n\n"
            . "Keluhan Anda telah mendapatkan teknisi:\n\n"
            . "Judul: {$this->order->complaint_title}\n"
            . "Prioritas: {$this->order->priority}\n"
            . "Teknisi: {$techName}\n"
            . "Spesialisasi: {$techSpecialty}\n"
            . "No. WA Teknisi: {$techPhone}\n\n"
            . "Link: " . url('/complaints/' . $this->order->id) . "\n\n"
            . "Teknisi akan segera menghubungi Anda. Mohon bersiap.";
    }

    public function toDatabase(object $notifiable): array
    {
        $tech = $this->order->technician;

        return [
            'title' => 'Teknisi Ditugaskan',
            'message' => "Teknisi {$tech->name} ditugaskan untuk laporan #{$this->order->id}",
            'order_id' => $this->order->id,
            'url' => url('/complaints/' . $this->order->id),
            'icon' => 'fas fa-user-cog',
            'color' => 'blue',
        ];
    }
}
