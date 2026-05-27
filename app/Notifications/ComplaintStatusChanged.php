<?php

namespace App\Notifications;

use App\Models\MaintenanceOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ComplaintStatusChanged extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceOrder $order, public string $oldStatus, public string $newStatus)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $statusLabels = [
            'waiting_approval' => 'Menunggu Persetujuan',
            'pending' => 'Pending',
            'scheduled' => 'Terjadwal',
            'in_progress' => 'Sedang Dikerjakan',
            'on_hold' => 'Ditunda',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            'reopened' => 'Dibuka Kembali',
            'cancelled' => 'Dibatalkan',
        ];

        $oldLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
        $newLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return (new MailMessage)
            ->subject('Perubahan Status Laporan #' . $this->order->id)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Status laporan keluhan Anda telah berubah.')
            ->line('Judul: ' . $this->order->complaint_title)
            ->line('Status: ' . $oldLabel . ' → ' . $newLabel)
            ->action('Lihat Detail', url('/complaints/' . $this->order->id))
            ->line('Terima kasih telah menggunakan layanan kami.');
    }
}
