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
        $channels = ['mail', 'database'];
        if ($notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
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

    public function toWhatsApp(object $notifiable): string
    {
        $statusLabels = [
            'waiting_approval' => 'Menunggu Persetujuan',
            'pending' => 'Telah Terdaftar',
            'scheduled' => 'Terjadwal',
            'in_progress' => 'Sedang Dikerjakan',
            'on_hold' => 'Ditunda',
            'done' => 'Selesai',
            'rejected' => 'Ditolak',
            'reopened' => 'Dibuka Kembali',
            'cancelled' => 'Dibatalkan',
        ];
        $newLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        $message = "*Status Laporan #{$this->order->id}*\n\n"
            . "Halo {$notifiable->name},\n\n";

        if ($this->oldStatus === 'new') {
            $message .= "Keluhan Anda berhasil dikirim dan sedang menunggu diproses oleh tim kami.\n\n";
        } else {
            $oldLabel = $statusLabels[$this->oldStatus] ?? $this->oldStatus;
            $message .= "Status laporan Anda berubah dari *{$oldLabel}* menjadi *{$newLabel}*.\n\n";
        }

        $message .= "Judul: {$this->order->complaint_title}\n"
            . "Deskripsi: {$this->order->complaint_description}\n\n"
            . "Link: " . url('/complaints/' . $this->order->id) . "\n\n"
            . "Terima kasih telah menggunakan Resident Help.";

        return $message;
    }

    public function toDatabase(object $notifiable): array
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
        $newLabel = $statusLabels[$this->newStatus] ?? $this->newStatus;

        return [
            'title' => 'Perubahan Status Laporan',
            'message' => "Status laporan #{$this->order->id} berubah menjadi: {$newLabel}",
            'order_id' => $this->order->id,
            'url' => url('/complaints/' . $this->order->id),
            'icon' => 'fas fa-exchange-alt',
            'color' => 'blue',
        ];
    }
}
