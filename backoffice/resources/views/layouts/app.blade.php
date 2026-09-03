<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Malega Apparel — Backoffice Dashboard' }}</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,600&family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-navy font-sans text-ivory antialiased selection:bg-gold selection:text-navy" x-data="{ sidebarOpen: false }">

<div class="flex min-h-screen">

    <!-- Mobile Overlay Backdrop -->
    <div 
        x-show="sidebarOpen" 
        x-transition:enter="transition-opacity ease-linear duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="sidebarOpen = false" 
        class="fixed inset-0 bg-black/60 backdrop-blur-xs z-30 lg:hidden"
        style="display: none;"
    ></div>

    <!-- Sidebar Navigation (Sticky / Fixed on Desktop) -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-40 w-56 shrink-0 bg-navy border-r border-gold/15 flex flex-col justify-between transition-transform duration-250 ease-in-out lg:sticky lg:top-0 lg:h-screen"
    >
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Brand Header -->
            <div class="h-16 shrink-0 flex flex-col items-center justify-center border-b border-gold/15 px-4">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center group">
                    <p class="font-display text-lg tracking-[0.2em] text-ivory group-hover:text-gold transition-colors">MALEGA</p>
                    <p class="text-[9px] tracking-[0.4em] text-gold mt-0.5 font-semibold">APPAREL</p>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                <p class="px-3 mb-1.5 text-[9px] font-semibold tracking-[0.2em] text-ivory/40 uppercase font-mono">Menu Utama</p>

                <!-- Dashboard -->
                <a 
                    href="{{ route('dashboard') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('dashboard') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l1.5-1.5m0 0l6.75-6.75L18.75 10.5m-13.5 0v8.25a1.5 1.5 0 001.5 1.5h3.75v-6h3v6h3.75a1.5 1.5 0 001.5-1.5V10.5m-13.5 0l1.5-1.5"/>
                    </svg>
                    <span class="text-xs">Dashboard</span>
                </a>

                <!-- Produk (Katalog) -->
                <a 
                    href="{{ route('catalog.products') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('catalog.products*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('catalog.products*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span class="text-xs">Produk & SKU</span>
                </a>

                <!-- Kategori -->
                <a 
                    href="{{ route('catalog.categories') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('catalog.categories*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('catalog.categories*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                    </svg>
                    <span class="text-xs">Kategori</span>
                </a>

                <!-- Spesifikasi Bahan -->
                <a 
                    href="{{ route('catalog.fabric-specs') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('catalog.fabric-specs*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('catalog.fabric-specs*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    <span class="text-xs">Spesifikasi Bahan</span>
                </a>

                <!-- Inventori -->
                <a 
                    href="{{ route('inventory.index') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('inventory*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('inventory*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 3.75c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
                    </svg>
                    <span class="text-xs">Inventori & Stok</span>
                </a>

                <!-- Pesanan -->
                <a 
                    href="{{ route('orders.index') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('orders*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('orders*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.881-4.804 2.231-7.454a1.125 1.125 0 00-1.12-1.296H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <span class="text-xs">Pesanan</span>
                    @php
                        $pendingOrdersCount = \App\Models\Order::where('order_status', \App\Enums\OrderStatus::Pending)->count();
                    @endphp
                    @if($pendingOrdersCount > 0)
                        <span class="ml-auto text-[9px] bg-gold text-navy font-bold px-1.5 py-0.5 rounded-full">{{ $pendingOrdersCount }}</span>
                    @endif
                </a>

                <!-- Pelanggan -->
                <a 
                    href="{{ route('customers.index') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('customers*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('customers*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-xs">Pelanggan</span>
                </a>

                <!-- GRUP KEUANGAN (FINANCE & TREASURY) -->
                <p class="px-3 pt-5 mb-1.5 text-[9px] font-semibold tracking-[0.2em] text-gold uppercase font-mono">Keuangan</p>

                <!-- Logs Pembayaran -->
                <a 
                    href="{{ route('finance.payment-logs') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('finance.payment-logs*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('finance.payment-logs*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" />
                    </svg>
                    <span class="text-xs">Logs Pembayaran</span>
                    @php
                        $pendingPaymentsCount = \App\Models\Payment::where('status', 'pending')->count();
                    @endphp
                    @if($pendingPaymentsCount > 0)
                        <span class="ml-auto text-[9px] bg-amber-500/20 text-amber-400 font-bold px-1.5 py-0.5 rounded-full animate-pulse border border-amber-500/30">{{ $pendingPaymentsCount }}</span>
                    @endif
                </a>

                <!-- Arus Kas -->
                <a 
                    href="{{ route('finance.cash-flow') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('finance.cash-flow*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('finance.cash-flow*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-xs">Arus Kas</span>
                </a>

                <!-- Laporan Keuangan -->
                <a 
                    href="{{ route('finance.reports') }}" 
                    class="flex items-center gap-2.5 px-3 py-2 rounded-md {{ request()->routeIs('finance.reports*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('finance.reports*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" />
                    </svg>
                    <span class="text-xs">Laporan Keuangan</span>
                </a>

                <p class="px-3 pt-5 mb-1.5 text-[9px] font-semibold tracking-[0.2em] text-ivory/40 uppercase font-mono">Pengaturan</p>
            </nav>
        </div>

        <!-- User Profile Card at Bottom of Sidebar -->
        <div class="p-4 border-t border-gold/15 shrink-0">
            @auth
                <div class="flex items-center justify-between gap-2 rounded-lg bg-white/5 px-3 py-2.5">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center text-navy font-semibold text-xs shrink-0 shadow-sm">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-medium text-ivory truncate">{{ auth()->user()->name }}</p>
                            <p class="text-[11px] text-gold/80 font-mono truncate">{{ auth()->user()->role?->label() ?? 'Staff' }}</p>
                        </div>
                    </div>

                    <!-- Trigger Confirmation Modal for Logout -->
                    <button 
                        type="button" 
                        @click="$dispatch('open-confirmation-logout-modal')"
                        title="Keluar dari sistem" 
                        class="text-ivory/40 hover:text-rose-400 p-1.5 rounded-lg hover:bg-white/5 transition-colors cursor-pointer shrink-0"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                        </svg>
                    </button>
                </div>
            @else
                <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 rounded-lg bg-gold text-navy font-semibold text-xs py-2 px-3">
                    Masuk ke Sistem &rarr;
                </a>
            @endauth
        </div>
    </aside>

    <!-- Main Viewport Area -->
    <div class="flex-1 min-w-0 flex flex-col min-h-screen">

        <!-- Topbar (Sticky Header) -->
        <header class="h-16 sticky top-0 z-20 bg-navy/95 backdrop-blur border-b border-gold/15 flex items-center gap-4 px-4 lg:px-6">
            <!-- Mobile Menu Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-ivory/70 hover:text-ivory p-1.5 -ml-1.5 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <!-- Breadcrumbs -->
            <div class="hidden md:block">
                <p class="text-[10px] text-ivory/40 tracking-wide font-mono">
                    @if(request()->routeIs('catalog.products*'))
                        Katalog / Produk & SKU
                    @elseif(request()->routeIs('catalog.categories*'))
                        Katalog / Kategori
                    @elseif(request()->routeIs('inventory*'))
                        Inventori / Saldo Stok & Buku Besar
                    @elseif(request()->routeIs('orders*'))
                        Transaksi / Manajemen Pesanan
                    @elseif(request()->routeIs('customers*'))
                        Pelanggan / Buku Kontak
                    @else
                        Overview / Dashboard
                    @endif
                </p>
            </div>

            <!-- Global Search Bar -->
            <div class="flex-1 max-w-sm ml-0 md:ml-6">
                <div class="relative">
                    <svg class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-ivory/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input 
                        type="text" 
                        placeholder="Cari pesanan, produk..." 
                        class="w-full bg-white/5 border border-white/10 rounded-full py-1.5 pl-8 pr-3 text-xs text-ivory placeholder:text-ivory/30 focus:outline-none focus:border-gold/50 transition-colors"
                    >
                </div>
            </div>

            <!-- Notifications & Profile Dropdown -->
            <div class="flex items-center gap-3 ml-auto">
                <button class="relative text-ivory/60 hover:text-ivory transition-colors p-1 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-0.5 right-0.5 w-1.5 h-1.5 rounded-full bg-gold"></span>
                </button>

                @auth
                    <button 
                        type="button"
                        @click="$dispatch('open-confirmation-logout-modal')"
                        class="w-7 h-7 rounded-full bg-gradient-to-br from-gold to-gold-dark hidden sm:flex items-center justify-center text-navy font-bold text-[10px] shadow-sm hover:ring-2 hover:ring-gold/50 transition-all cursor-pointer"
                        title="{{ auth()->user()->name }} (Klik untuk logout)"
                    >
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </button>
                @endauth
            </div>
        </header>

        <!-- Main Page Content Slot -->
        <main class="flex-1 p-4 lg:p-6 space-y-5">
            {{ $slot }}
        </main>
    </div>
</div>

<!-- Dedicated Hidden Logout Form -->
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
    @csrf
</form>

<!-- Reusable Logout Confirmation Modal -->
<x-confirmation-modal
    id="logout-modal"
    title="Konfirmasi Keluar Sistem"
    message="Apakah Anda yakin ingin keluar dari sistem Backoffice Malega Apparel? Sesi aktif Anda akan diakhiri."
    confirmText="Ya, Keluar"
    cancelText="Batal"
    type="danger"
    icon="logout"
>
    <x-slot:action>
        <button
            type="button"
            onclick="document.getElementById('logout-form').submit();"
            class="w-full sm:w-auto px-5 py-2.5 rounded-xl text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow-lg shadow-rose-950/30 transition-all cursor-pointer focus:outline-none focus:ring-2 focus:ring-rose-500"
        >
            Ya, Keluar Sekarang
        </button>
    </x-slot:action>
</x-confirmation-modal>

<!-- Reusable Generic Delete Confirmation Modal -->
<x-confirmation-modal
    id="delete-modal"
    title="Konfirmasi Hapus Data"
    message="Tindakan ini tidak dapat dibatalkan. Apakah Anda yakin ingin menghapus data yang dipilih?"
    confirmText="Ya, Hapus Data"
    cancelText="Batal"
    type="danger"
    icon="delete"
/>

<!-- Modern Toast Notification Container in Bottom-Right Corner -->
<x-toast />

@livewireScripts
</body>
</html>
