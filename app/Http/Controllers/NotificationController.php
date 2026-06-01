<?php

namespace App\Http\Controllers;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->latest()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        $redirectUrl = request('redirect');
        $url = $redirectUrl ? urldecode($redirectUrl) : ($notification->data['url'] ?? url('/dashboard'));
        return redirect($url);
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Semua notifikasi telah dibaca.');
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => Auth::user()->unreadNotifications->count()
        ]);
    }

    public function fetchLatest()
    {
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();
        $unreadCount = Auth::user()->unreadNotifications->count();

        $html = '';
        foreach ($notifications as $notif) {
            $data = $notif->data;
            $icon = $data['icon'] ?? 'fas fa-bell';
            $color = $data['color'] ?? 'gray';
            $url = $data['url'] ?? '#';
            $title = $data['title'] ?? 'Notifikasi';
            $message = $data['message'] ?? '';
            $time = $notif->created_at->diffForHumans();

            $bgClass = $notif->read_at ? 'bg-gray-50' : 'bg-blue-50';
            $readUrl = route('notifications.read', $notif->id) . '?redirect=' . urlencode($url);
            $html .= "<a href=\"{$readUrl}\" class=\"block px-4 py-3 {$bgClass} hover:bg-gray-100 transition border-b\">
                <div class=\"flex items-center\">
                    <div class=\"w-8 h-8 rounded-full flex items-center justify-center text-white bg-{$color}-500 mr-3\">
                        <i class=\"{$icon} text-xs\"></i>
                    </div>
                    <div class=\"flex-1\">
                        <p class=\"text-sm font-semibold text-gray-800\">{$title}</p>
                        <p class=\"text-xs text-gray-600 truncate\">{$message}</p>
                        <p class=\"text-xs text-gray-400 mt-1\">{$time}</p>
                    </div>
                </div>
            </a>";
        }

        return response()->json([
            'html' => $html,
            'unread_count' => $unreadCount,
        ]);
    }
}
