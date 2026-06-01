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
        $channels = ['mail', 'database'];
        if ($notifiable->phone) {
            $channels[] = WhatsAppChannel::class;
        }
        return $channels;
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

    public function toWhatsApp(object $notifiable): string
    {
        return "*Estimasi Biaya Disetujui ✅*\n\n"
            . "Halo {$notifiable->name},\n\n"
            . "Estimasi biaya untuk laporan Anda telah disetujui:\n"
            . "Judul: {$this->order->complaint_title}\n"
            . "Biaya: Rp " . number_format($this->order->estimated_cost, 0, ',', '.') . "\n\n"
            . "Lihat detail: " . url('/complaints/' . $this->order->id) . "\n\n"
            . "Silakan lakukan pembayaran.";
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Estimasi Biaya Disetujui',
            'message' => "Estimasi biaya laporan #{$this->order->id}: Rp " . number_format($this->order->estimated_cost, 0, ',', '.'),
            'order_id' => $this->order->id,
            'url' => url('/complaints/' . $this->order->id),
            'icon' => 'fas fa-money-bill-wave',
            'color' => 'green',
        ];
    }
}
