<header class="flex justify-between items-center py-4 px-6 bg-white border-b-4 border-indigo-600">
    <div class="flex items-center">
        <button @click="sidebarOpen = true" class="text-gray-500 focus:outline-none lg:hidden">
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
    </div>

    <div class="flex items-center space-x-4">
        {{-- NOTIFIKASI BELL --}}
        <div x-data="{ notifOpen: false, unread: 0, notifHtml: '' }" class="relative"
            @click.outside="notifOpen = false">

            <button @click="
                notifOpen = !notifOpen;
                if(notifOpen) {
                    fetch('{{ route('notifications.fetch') }}')
                        .then(r => r.json())
                        .then(d => { unread = d.unread_count; notifHtml = d.html; });
                }
            " class="relative text-gray-500 hover:text-indigo-600 transition">
                <i class="fas fa-bell text-xl"></i>
                <template x-if="unread > 0">
                    <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold"
                        x-text="unread"></span>
                </template>
            </button>

            <div x-show="notifOpen"
                class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl border z-50"
                style="display:none;">
                <div class="p-3 border-b flex justify-between items-center bg-gray-50 rounded-t-lg">
                    <span class="font-bold text-sm text-gray-700">Notifikasi</span>
                    <a href="{{ route('notifications.index') }}" class="text-xs text-blue-600 hover:underline">Lihat Semua</a>
                </div>
                <div class="max-h-96 overflow-y-auto" x-html="notifHtml">
                    <div class="px-4 py-6 text-center text-gray-400 text-sm">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Memuat...
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Auto-mark notification as read when page loads with ?notif_id=
            const params = new URLSearchParams(window.location.search);
            const notifId = params.get('notif_id');
            if (notifId) {
                fetch('{{ url('/notifications') }}/' + notifId + '/read', { method: 'GET' })
                    .then(() => {
                        history.replaceState({}, '', window.location.pathname);
                    });
            }
        </script>

        {{-- USER DROPDOWN --}}
        <x-dropdown>
            <x-slot name="trigger">
                <button @click="dropdownOpen = ! dropdownOpen" class="relative block overflow-hidden font-semibold text-gray-700 hover:text-indigo-600">
                    {{ Auth::user()->name }}
                </button>
            </x-slot>

            <x-slot name="content">
                <x-dropdown-link href="{{ route('profile.edit') }}">
                    {{ __('Profile') }}
                </x-dropdown-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-dropdown-link href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Log out') }}
                    </x-dropdown-link>
                </form>
            </x-slot>
        </x-dropdown>
    </div>
</header>

{{-- Alpine.js for header --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch initial unread count
        fetch('{{ route('notifications.unread') }}')
            .then(r => r.json())
            .then(d => {
                document.querySelectorAll('[x-data]').forEach(el => {
                    if (el.__x) el.__x.$data.unread = d.count;
                });
            });
    });
</script>
