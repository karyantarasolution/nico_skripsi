<div :class="sidebarOpen ? 'block' : 'hidden'" @click="sidebarOpen = false"
    class="fixed z-20 inset-0 bg-black opacity-50 transition-opacity lg:hidden"></div>

<div :class="sidebarOpen ? 'translate-x-0 ease-out' : '-translate-x-full ease-in'"
    class="fixed z-30 inset-y-0 left-0 w-64 transition duration-300 transform bg-gray-900 overflow-y-auto lg:translate-x-0 lg:static lg:inset-0">

    <div class="flex items-center justify-center mt-8">
        <div class="flex items-center">
            <img src="{{ asset('logo/logo.jpg') }}" alt="Logo" class="w-12 h-12 rounded-full">
            <span class="mx-2 text-2xl font-semibold text-white">{{ __('Resident Help') }}</span>
        </div>
    </div>

    <nav class="mt-10">
        <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            <x-slot name="icon">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
            </x-slot>
            {{ __('Dashboard') }}
        </x-nav-link>

        <x-nav-link href="{{ route('notifications.index') }}" :active="request()->routeIs('notifications.*')">
            <x-slot name="icon">
                <i class="fas fa-bell w-6 h-6 flex items-center justify-center text-lg"></i>
            </x-slot>
            {{ __('Notifikasi') }}
        </x-nav-link>

        @if (Auth::user()->role === 'admin')
            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Master Data
            </div>

            <x-nav-link href="{{ route('admin.users.index') }}" :active="request()->routeIs('admin.users.*')">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </x-slot>
                {{ __('Data Pengguna') }}
            </x-nav-link>

            <x-nav-link href="{{ route('admin.technicians.index') }}" :active="request()->routeIs('admin.technicians.*')">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </x-slot>
                {{ __('Data Tukang') }}
            </x-nav-link>

            <x-nav-link href="{{ route('admin.prices.index') }}" :active="request()->routeIs('admin.prices.*')">
                <x-slot name="icon">
                    <i class="fas fa-tags w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Master Harga') }}
            </x-nav-link>

            <x-nav-link href="{{ route('admin.units.index') }}" :active="request()->routeIs('admin.units.*')">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </x-slot>
                {{ __('Data Unit Rumah') }}
            </x-nav-link>

            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Transaksi
            </div>

            <x-nav-link href="{{ route('admin.customers.index') }}" :active="request()->routeIs('admin.customers.*')">
                <x-slot name="icon">
                    <i class="fas fa-users w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Data Nasabah') }}
            </x-nav-link>

            <x-nav-link href="{{ route('admin.ownerships.index') }}" :active="request()->routeIs('admin.ownerships.*')">
                <x-slot name="icon">
                    <i class="fas fa-handshake w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Data Kepemilikan') }}
            </x-nav-link>

            <x-nav-link href="{{ route('admin.maintenance.index') }}" :active="request()->routeIs('admin.maintenance.*')">
                <x-slot name="icon">
                    <i class="fas fa-tools w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Keluhan Masuk') }}
            </x-nav-link>

            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Pemeliharaan
            </div>

            <x-nav-link href="{{ route('admin.scheduled-maintenance.index') }}" :active="request()->routeIs('admin.scheduled-maintenance.*')">
                <x-slot name="icon">
                    <i class="fas fa-calendar-alt w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Jadwal Pemeliharaan') }}
            </x-nav-link>

            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Laporan & Output
            </div>

            <x-nav-link href="{{ route('admin.reports.index') }}" :active="request()->routeIs('admin.reports.*')">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </x-slot>
                {{ __('Pusat Laporan') }}
            </x-nav-link>
        @endif

        @if (Auth::user()->role === 'teknisi')
            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Menu Teknisi
            </div>

            <x-nav-link href="{{ route('technician.maintenance.index') }}" :active="request()->routeIs('technician.maintenance.*')">
                <x-slot name="icon">
                    <i class="fas fa-tools w-6 h-6 flex items-center justify-center text-lg"></i>
                </x-slot>
                {{ __('Keluhan Masuk') }}
            </x-nav-link>
        @endif

        @if (in_array(Auth::user()->role, ['nasabah', 'warga']))
            <div class="mt-4 mb-2 px-6 text-xs text-gray-400 uppercase tracking-wider">
                Menu Warga
            </div>

            @if (Auth::user()->role === 'nasabah')
                <x-nav-link href="{{ route('my.assets') }}" :active="request()->routeIs('my.assets')">
                    <x-slot name="icon">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </x-slot>
                    {{ __('Rumah Saya') }}
                </x-nav-link>
            @endif

            <x-nav-link href="{{ route('complaints.index') }}" :active="request()->routeIs('complaints.*')">
                <x-slot name="icon">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </x-slot>
                {{ __('Lapor Keluhan') }}
            </x-nav-link>
        @endif
    </nav>
</div>
