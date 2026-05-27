<?php

namespace App\Notifications;

use App\Models\MaintenanceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CostEstimationApproved extends Notification
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
            ->subject('Estimasi Biaya Disetujui - Laporan #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Estimasi biaya perbaikan untuk laporan Anda telah disetujui.')
            ->line('Judul: ' . $this->order->complaint_title)
            ->line('Estimasi Biaya: Rp ' . number_format($this->order->estimated_cost, 0, ',', '.'))
            ->line('Keterangan: ' . ($this->order->estimated_description ?? '-'))
            ->action('Lihat Detail', url('/complaints/' . $this->order->id))
            ->line('Silakan lakukan pembayaran setelah perbaikan selesai.');
    }
}
