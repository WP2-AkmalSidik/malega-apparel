'use client';

import React from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { Home, Compass, Search, Truck, User } from 'lucide-react';
import { useAuth } from '../context/AuthContext';

interface MobileBottomNavProps {
  onOpenSearch: () => void;
  isSearchOpen?: boolean;
}

export default function MobileBottomNav({ onOpenSearch, isSearchOpen = false }: MobileBottomNavProps) {
  const pathname = usePathname();
  const { customer, isAuthenticated } = useAuth();

  // Hide mobile bottom nav on product detail (which has its own action bar) and checkout
  const isProductDetail = pathname.startsWith('/products/');
  const isCheckout = pathname.startsWith('/checkout');

  if (isProductDetail || isCheckout) {
    return null;
  }

  const isHomeActive = pathname === '/';
  const isKoleksiActive = pathname.startsWith('/katalog');
  const isTrackActive = pathname.startsWith('/track');
  const isAccountActive = pathname.startsWith('/account') || pathname.startsWith('/login');

  const displayName = customer?.name ? customer.name.split(' ')[0] : 'Akun';

  return (
    <nav
      aria-label="Navigasi Aplikasi Mobile"
      className="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-[#080E20]/95 backdrop-blur-2xl border-t border-[#CBAC70]/20 shadow-[0_-8px_30px_rgba(0,0,0,0.65)] safe-bottom select-none"
    >
      <div className="grid grid-cols-5 h-16 max-w-lg mx-auto items-center px-1">
        
        {/* 1. Home / Beranda */}
        <Link
          href="/"
          className={`flex flex-col items-center justify-center h-full gap-1 transition-all duration-200 active:scale-95 group relative ${
            isHomeActive ? 'text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200'
          }`}
        >
          {isHomeActive && (
            <span className="absolute top-0 w-8 h-[2.5px] bg-[#CBAC70] rounded-b-full shadow-[0_2px_8px_#CBAC70]" />
          )}
          <Home
            className={`w-5 h-5 transition-transform duration-200 ${
              isHomeActive ? 'scale-110 stroke-[2.3]' : 'group-hover:scale-105'
            }`}
          />
          <span
            className={`text-[9.5px] tracking-wider uppercase leading-none ${
              isHomeActive ? 'font-black text-[#CBAC70]' : 'font-medium'
            }`}
          >
            Home
          </span>
        </Link>

        {/* 2. Koleksi / Katalog */}
        <Link
          href="/katalog"
          className={`flex flex-col items-center justify-center h-full gap-1 transition-all duration-200 active:scale-95 group relative ${
            isKoleksiActive ? 'text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200'
          }`}
        >
          {isKoleksiActive && (
            <span className="absolute top-0 w-8 h-[2.5px] bg-[#CBAC70] rounded-b-full shadow-[0_2px_8px_#CBAC70]" />
          )}
          <Compass
            className={`w-5 h-5 transition-transform duration-200 ${
              isKoleksiActive ? 'scale-110 stroke-[2.3]' : 'group-hover:scale-105'
            }`}
          />
          <span
            className={`text-[9.5px] tracking-wider uppercase leading-none ${
              isKoleksiActive ? 'font-black text-[#CBAC70]' : 'font-medium'
            }`}
          >
            Koleksi
          </span>
        </Link>

        {/* 3. Pencarian Cepat (Modal Trigger) */}
        <button
          type="button"
          onClick={onOpenSearch}
          className={`flex flex-col items-center justify-center h-full gap-1 transition-all duration-200 active:scale-95 group relative cursor-pointer ${
            isSearchOpen ? 'text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200'
          }`}
          aria-label="Pencarian Cepat"
        >
          {isSearchOpen && (
            <span className="absolute top-0 w-8 h-[2.5px] bg-[#CBAC70] rounded-b-full shadow-[0_2px_8px_#CBAC70]" />
          )}
          <div className="p-1 rounded-xl bg-white/5 border border-white/10 group-hover:border-[#CBAC70]/40 group-hover:bg-[#CBAC70]/10 transition-all">
            <Search className="w-4 h-4 text-[#CBAC70]" />
          </div>
          <span
            className={`text-[9.5px] tracking-wider uppercase leading-none ${
              isSearchOpen ? 'font-black text-[#CBAC70]' : 'font-medium'
            }`}
          >
            Cari
          </span>
        </button>

        {/* 4. Lacak Pengiriman */}
        <Link
          href="/track"
          className={`flex flex-col items-center justify-center h-full gap-1 transition-all duration-200 active:scale-95 group relative ${
            isTrackActive ? 'text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200'
          }`}
        >
          {isTrackActive && (
            <span className="absolute top-0 w-8 h-[2.5px] bg-[#CBAC70] rounded-b-full shadow-[0_2px_8px_#CBAC70]" />
          )}
          <Truck
            className={`w-5 h-5 transition-transform duration-200 ${
              isTrackActive ? 'scale-110 stroke-[2.3]' : 'group-hover:scale-105'
            }`}
          />
          <span
            className={`text-[9.5px] tracking-wider uppercase leading-none ${
              isTrackActive ? 'font-black text-[#CBAC70]' : 'font-medium'
            }`}
          >
            Lacak
          </span>
        </Link>

        {/* 5. Profil / Akun Member */}
        <Link
          href={isAuthenticated ? '/account' : '/login'}
          className={`flex flex-col items-center justify-center h-full gap-1 transition-all duration-200 active:scale-95 group relative ${
            isAccountActive ? 'text-[#CBAC70]' : 'text-slate-400 hover:text-slate-200'
          }`}
        >
          {isAccountActive && (
            <span className="absolute top-0 w-8 h-[2.5px] bg-[#CBAC70] rounded-b-full shadow-[0_2px_8px_#CBAC70]" />
          )}
          <div className="relative">
            {isAuthenticated && customer ? (
              <div className="w-5 h-5 rounded-full bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-[9px] shadow-sm">
                {customer.name.substring(0, 1).toUpperCase()}
              </div>
            ) : (
              <User
                className={`w-5 h-5 transition-transform duration-200 ${
                  isAccountActive ? 'scale-110 stroke-[2.3]' : 'group-hover:scale-105'
                }`}
              />
            )}
          </div>
          <span
            className={`text-[9.5px] tracking-wider uppercase leading-none max-w-[56px] truncate ${
              isAccountActive ? 'font-black text-[#CBAC70]' : 'font-medium'
            }`}
          >
            {isAuthenticated ? displayName : 'Masuk'}
          </span>
        </Link>

      </div>
    </nav>
  );
}
