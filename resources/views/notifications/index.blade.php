<x-app-layout>
    <x-slot name="header">
        {{ __('Notifikasi Saya') }}
    </x-slot>

    <div class="bg-white rounded-lg shadow-md">
        <div class="p-4 border-b flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Semua Notifikasi</h3>
            @if (Auth::user()->unreadNotifications->count() > 0)
                <form action="{{ route('notifications.markAllRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-sm text-blue-600 hover:underline">
                        <i class="fas fa-check-double mr-1"></i> Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="divide-y">
            @forelse ($notifications as $notif)
                @php
                    $data = $notif->data;
                    $icon = $data['icon'] ?? 'fas fa-bell';
                    $color = $data['color'] ?? 'gray';
                    $url = $data['url'] ?? '#';
                    $title = $data['title'] ?? 'Notifikasi';
                    $message = $data['message'] ?? '';
                @endphp
                <a href="{{ route('notifications.read', $notif->id) }}"
                    class="block px-6 py-4 {{ $notif->read_at ? '' : 'bg-blue-50 border-l-4 border-blue-500' }} hover:bg-gray-50 transition">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white bg-{{ $color }}-500 mr-4">
                            <i class="{{ $icon }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-gray-800 {{ $notif->read_at ? '' : 'font-bold' }}">{{ $title }}</p>
                            <p class="text-sm text-gray-600">{{ $message }}</p>
                            <p class="text-xs text-gray-400 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>
                        @if (!$notif->read_at)
                            <span class="w-2 h-2 bg-blue-500 rounded-full"></span>
                        @endif
                    </div>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-gray-500">
                    <i class="fas fa-bell-slash text-4xl mb-3 text-gray-300"></i>
                    <p>Tidak ada notifikasi.</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 border-t">
            {{ $notifications->links() }}
        </div>
    </div>
</x-app-layout>
