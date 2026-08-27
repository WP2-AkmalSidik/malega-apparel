'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { ShoppingBag, Search, Menu, X, ArrowUpRight } from 'lucide-react';
import { useCart } from '../context/CartContext';
import BrandLogo from './BrandLogo';

export default function Navbar() {
  const pathname = usePathname();
  const { cartCount, setIsCartOpen } = useCart();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  const navLinks = [
    { label: 'Home', href: '/' },
    { label: 'Katalog', href: '/katalog' },
  ];

  return (
    <>
      {/* Top Luxury Announcement Bar (Slim & Minimal) */}
      <div className="bg-[#080E20] border-b border-[#CBAC70]/20 text-[#CBAC70] text-[8.5px] xs:text-[9.5px] sm:text-[10.5px] py-1 px-3 sm:px-6 font-medium tracking-wide">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-1.5 sm:gap-2 leading-tight overflow-hidden">
            <span className="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full bg-[#CBAC70] animate-pulse shrink-0"></span>
            <span className="text-[#FDFCFF] font-bold tracking-wider shrink-0">GRATIS ONGKIR:</span>
            <span className="truncate text-[#CBAC70]">Gunakan Kode &quot;FREESHIPXTRA&quot; Seluruh ID</span>
          </div>

          <div className="hidden sm:flex items-center gap-3 text-[10.5px] text-[#94A3B8]">
            <span className="text-[#CBAC70] font-semibold">100% Original Bespoke Studio</span>
            <span>•</span>
            <span>Bandung, ID</span>
          </div>
        </div>
      </div>

      {/* Main Luxury Header (Slim & Compact Height) */}
      <header className="sticky top-0 z-40 bg-[#0B132B]/95 backdrop-blur-xl border-b border-[#CBAC70]/20">
        <div className="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 h-13 sm:h-14 lg:h-16 flex items-center justify-between gap-4 sm:gap-6">
          
          {/* Left: Brand Logo (Kept full size & sharp) */}
          <div className="flex items-center">
            <BrandLogo size="md" />
          </div>

          {/* Center: Desktop Navigation Links */}
          <nav className="hidden lg:flex items-center gap-8 text-xs font-bold uppercase tracking-[0.2em] text-[#94A3B8]">
            {navLinks.map((link) => {
              const isActive = pathname === link.href || (link.href !== '/' && pathname.startsWith(link.href));
              return (
                <Link
                  key={link.label}
                  href={link.href}
                  className={`transition-all duration-200 py-1 hover:text-[#CBAC70] relative ${
                    isActive ? 'text-[#CBAC70] font-black' : ''
                  }`}
                >
                  {link.label}
                  {isActive && (
                    <span className="absolute bottom-0 left-0 right-0 h-[2px] bg-[#CBAC70] rounded-full shadow-[0_0_8px_#CBAC70]" />
                  )}
                </Link>
              );
            })}
          </nav>

          {/* Right Utilities (Compact) */}
          <div className="flex items-center gap-2 sm:gap-3">
            
            {/* Search Trigger */}
            <button
              onClick={() => setSearchOpen(true)}
              className="p-2 rounded-full text-[#FDFCFF]/80 hover:text-[#CBAC70] hover:bg-[#14204A] transition-colors"
              title="Search Catalog"
            >
              <Search className="w-4 h-4 sm:w-4.5 sm:h-4.5" />
            </button>

            {/* Shopping Bag Button */}
            <button
              onClick={() => setIsCartOpen(true)}
              className="relative p-2 rounded-full text-[#FDFCFF] hover:bg-[#14204A] border border-[#CBAC70]/30 hover:border-[#CBAC70] transition-all group active:scale-95"
              aria-label="Open Shopping Bag"
            >
              <ShoppingBag className="w-4 h-4 sm:w-4.5 sm:h-4.5 group-hover:text-[#CBAC70] transition-colors" />
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center shadow-lg border border-[#0B132B]">
                  {cartCount}
                </span>
              )}
            </button>

            {/* Mobile Menu Toggle */}
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-1.5 rounded-lg text-[#FDFCFF] hover:bg-[#14204A] transition-colors"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>

          </div>

        </div>

        {/* Mobile Navigation Drawer */}
        {mobileMenuOpen && (
          <div className="lg:hidden bg-[#0B132B] border-b border-[#CBAC70]/30 px-5 py-4 space-y-3 animate-in slide-in-from-top-2">
            <nav className="flex flex-col space-y-2 text-xs font-bold uppercase tracking-wider text-[#94A3B8]">
              {navLinks.map((link) => (
                <Link
                  key={link.label}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className="py-2 hover:text-[#CBAC70] border-b border-white/5 flex items-center justify-between"
                >
                  <span>{link.label}</span>
                  <ArrowUpRight className="w-3.5 h-3.5 text-[#CBAC70]" />
                </Link>
              ))}
            </nav>

            <div className="pt-2.5 border-t border-white/10 flex items-center justify-between text-[10px] text-[#94A3B8]">
              <span>© 2026 MALEGA APPAREL</span>
              <span className="text-[#CBAC70] font-mono font-semibold">Official Storefront</span>
            </div>
          </div>
        )}
      </header>

      {/* Global Quick Search Modal */}
      {searchOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-start justify-center pt-20 px-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-2xl max-w-xl w-full p-5 shadow-2xl space-y-3.5 animate-in zoom-in-95">
            <div className="flex items-center justify-between border-b border-white/10 pb-2.5">
              <span className="text-xs font-bold uppercase tracking-widest text-[#CBAC70]">Search Malega Collection</span>
              <button onClick={() => setSearchOpen(false)} className="text-[#94A3B8] hover:text-white">✕</button>
            </div>

            <form onSubmit={(e) => { e.preventDefault(); if (searchQuery) window.location.href = `/?search=${encodeURIComponent(searchQuery)}`; }} className="relative">
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Cari artikel (misal: Heavyweight 300GSM, Cargo, Hoodie)..."
                className="w-full bg-[#0B132B] border border-[#CBAC70]/30 rounded-xl px-4 py-2.5 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
                autoFocus
              />
              <button type="submit" className="absolute right-2.5 top-2 px-3 py-1 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-lg hover:bg-[#E3CD99]">
                Cari
              </button>
            </form>
          </div>
        </div>
      )}
    </>
  );
}
