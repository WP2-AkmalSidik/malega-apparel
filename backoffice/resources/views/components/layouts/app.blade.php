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
        class="fixed inset-y-0 left-0 z-40 w-64 shrink-0 bg-navy border-r border-gold/15 flex flex-col justify-between transition-transform duration-250 ease-in-out lg:sticky lg:top-0 lg:h-screen"
    >
        <div class="flex flex-col flex-1 min-h-0">
            <!-- Brand Header -->
            <div class="h-20 shrink-0 flex flex-col items-center justify-center border-b border-gold/15 px-6">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center group">
                    <p class="font-display text-xl tracking-[0.2em] text-ivory group-hover:text-gold transition-colors">MALEGA</p>
                    <p class="text-[10px] tracking-[0.4em] text-gold mt-0.5 font-semibold">APPAREL</p>
                </a>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <p class="px-3 mb-2 text-[10px] font-semibold tracking-[0.2em] text-ivory/40 uppercase font-mono">Menu Utama</p>

                <!-- Dashboard -->
                <a 
                    href="{{ route('dashboard') }}" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md {{ request()->routeIs('dashboard') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('dashboard') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12l1.5-1.5m0 0l6.75-6.75L18.75 10.5m-13.5 0v8.25a1.5 1.5 0 001.5 1.5h3.75v-6h3v6h3.75a1.5 1.5 0 001.5-1.5V10.5m-13.5 0l1.5-1.5"/>
                    </svg>
                    <span class="text-sm">Dashboard</span>
                </a>

                <!-- Produk (Katalog) -->
                <a 
                    href="{{ route('catalog.products') }}" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md {{ request()->routeIs('catalog.products*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('catalog.products*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                    </svg>
                    <span class="text-sm">Produk & SKU</span>
                </a>

                <!-- Kategori -->
                <a 
                    href="{{ route('catalog.categories') }}" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md {{ request()->routeIs('catalog.categories*') ? 'bg-gold/10 border-l-2 border-gold text-ivory font-medium' : 'text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors' }}"
                >
                    <svg class="w-4 h-4 {{ request()->routeIs('catalog.categories*') ? 'text-gold' : 'text-ivory/60' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/>
                    </svg>
                    <span class="text-sm">Kategori</span>
                </a>

                <!-- Pesanan -->
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.881-4.804 2.231-7.454a1.125 1.125 0 00-1.12-1.296H5.25M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                    </svg>
                    <span class="text-sm">Pesanan</span>
                    <span class="ml-auto text-[10px] bg-gold text-navy font-semibold px-1.5 py-0.5 rounded-full">12</span>
                </a>

                <!-- Pelanggan -->
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                    <span class="text-sm">Pelanggan</span>
                </a>

                <!-- Inventori -->
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 3.75c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125"/>
                    </svg>
                    <span class="text-sm">Inventori</span>
                </a>

                <p class="px-3 pt-6 mb-2 text-[10px] font-semibold tracking-[0.2em] text-ivory/40 uppercase font-mono">Lainnya</p>

                <!-- Laporan -->
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                    </svg>
                    <span class="text-sm">Laporan</span>
                </a>

                <!-- Pengaturan -->
                <a 
                    href="#" 
                    class="flex items-center gap-3 px-3 py-2.5 rounded-md text-ivory/60 hover:bg-white/5 hover:text-ivory transition-colors"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm">Pengaturan</span>
                </a>
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
        <header class="h-20 sticky top-0 z-20 bg-navy/95 backdrop-blur border-b border-gold/15 flex items-center gap-4 px-4 lg:px-8">
            <!-- Mobile Menu Toggle Button -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-ivory/70 hover:text-ivory p-1.5 -ml-1.5 cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                </svg>
            </button>

            <!-- Breadcrumbs -->
            <div class="hidden md:block">
                <p class="text-[11px] text-ivory/40 tracking-wide font-mono">
                    {{ request()->routeIs('catalog.products*') ? 'Katalog / Produk & SKU' : (request()->routeIs('catalog.categories*') ? 'Katalog / Kategori' : 'Overview / Dashboard') }}
                </p>
            </div>

            <!-- Global Search Bar -->
            <div class="flex-1 max-w-md ml-0 md:ml-6">
                <div class="relative">
                    <svg class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-ivory/30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/>
                    </svg>
                    <input 
                        type="text" 
                        placeholder="Cari pesanan, produk, pelanggan..." 
                        class="w-full bg-white/5 border border-white/10 rounded-full py-2.5 pl-10 pr-4 text-sm text-ivory placeholder:text-ivory/30 focus:outline-none focus:border-gold/50 transition-colors"
                    >
                </div>
            </div>

            <!-- Notifications & Profile Dropdown -->
            <div class="flex items-center gap-4 ml-auto">
                <button class="relative text-ivory/60 hover:text-ivory transition-colors p-1 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                    <span class="absolute top-0.5 right-0.5 w-2 h-2 rounded-full bg-gold"></span>
                </button>

                @auth
                    <button 
                        type="button"
                        @click="$dispatch('open-confirmation-logout-modal')"
                        class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-dark hidden sm:flex items-center justify-center text-navy font-bold text-xs shadow-sm hover:ring-2 hover:ring-gold/50 transition-all cursor-pointer"
                        title="{{ auth()->user()->name }} (Klik untuk logout)"
                    >
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </button>
                @endauth
            </div>
        </header>

        <!-- Main Page Content Slot -->
        <main class="flex-1 p-4 lg:p-8 space-y-6">
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
