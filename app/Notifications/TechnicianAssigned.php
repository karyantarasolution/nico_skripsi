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
        return ['mail'];
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
}
