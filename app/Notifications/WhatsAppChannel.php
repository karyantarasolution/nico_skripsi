<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel
{
    public function send(object $notifiable, Notification $notification): void
    {
        if (!method_exists($notification, 'toWhatsApp')) {
            return;
        }

        $message = $notification->toWhatsApp($notifiable);

        $phone = $notifiable->phone;
        if (!$phone) {
            Log::warning('WA notif skipped: no phone for ' . $notifiable->email);
            return;
        }

        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }

        $apiKey = config('services.whatsapp.api_key');
        $apiUrl = config('services.whatsapp.api_url', 'https://api.fonnte.com/send');

        if (!$apiKey || $apiKey === 'YOUR_API_KEY') {
            Log::info('[WA SIMULATED] To: ' . $phone . ' | Message: ' . $message);
            return;
        }

        try {
            Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post($apiUrl, [
                'target' => $phone,
                'message' => $message,
                'countryCode' => '62',
            ]);
        } catch (\Throwable $e) {
            Log::warning('Gagal kirim WA: ' . $e->getMessage());
        }
    }
}
