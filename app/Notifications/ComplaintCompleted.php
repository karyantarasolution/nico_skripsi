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
        return ['mail'];
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
}
