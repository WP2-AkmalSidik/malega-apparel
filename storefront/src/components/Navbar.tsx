'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { ShoppingBag, Search, Menu, X, Shield, ArrowUpRight, ExternalLink } from 'lucide-react';
import BrandLogo from './BrandLogo';
import { useCart } from '../context/CartContext';

export default function Navbar() {
  const pathname = usePathname();
  const { cartCount, setIsCartOpen } = useCart();
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');

  const navLinks = [
    { label: 'Home', href: '/' },
    { label: 'Catalog / Shop', href: '/products' },
    { label: 'Heavyweight Tees', href: '/products?category=T-Shirts' },
    { label: 'Hoodies & Outer', href: '/products?category=Outerwear' },
    { label: 'Bottoms', href: '/products?category=Bottoms' },
    { label: 'Accessories', href: '/products?category=Accessories' }
  ];

  return (
    <>
      {/* Top Luxury Announcement Bar */}
      <div className="bg-[#080E20] border-b border-[#CBAC70]/20 text-[#CBAC70] text-[11px] py-1.5 px-4 font-medium tracking-wide">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span className="w-1.5 h-1.5 rounded-full bg-[#CBAC70] animate-pulse"></span>
            <span className="text-[#FDFCFF]">COMPLIMENTARY SHIPPING:</span>
            <span>Nikmati Gratis Ongkir XTRA Seluruh ID dengan Kode &quot;FREESHIPXTRA&quot;</span>
          </div>

          <div className="hidden sm:flex items-center gap-4 text-[11px] text-[#94A3B8]">
            <a 
              href="http://localhost:8000" 
              target="_blank" 
              rel="noopener noreferrer"
              className="hover:text-[#CBAC70] flex items-center gap-1 transition-colors"
            >
              <span>Backoffice Portal</span>
              <ExternalLink className="w-3 h-3" />
            </a>
            <span>|</span>
            <span className="text-[#CBAC70] font-semibold">100% Bespoke Craftsmanship</span>
          </div>
        </div>
      </div>

      {/* Main Luxury Header */}
      <header className="sticky top-0 z-40 bg-[#0B132B]/90 backdrop-blur-xl border-b border-[#CBAC70]/20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-6">
          
          {/* Left: Brand Logo */}
          <BrandLogo size="md" />

          {/* Center: Desktop Navigation Links */}
          <nav className="hidden lg:flex items-center gap-7 text-xs font-semibold uppercase tracking-[0.15em] text-[#94A3B8]">
            {navLinks.map((link) => {
              const isActive = pathname === link.href || (link.href !== '/' && pathname.startsWith(link.href));
              return (
                <Link
                  key={link.label}
                  href={link.href}
                  className={`transition-all duration-200 py-1 hover:text-[#CBAC70] relative ${
                    isActive ? 'text-[#CBAC70] font-bold' : ''
                  }`}
                >
                  {link.label}
                  {isActive && (
                    <span className="absolute bottom-0 left-0 right-0 h-[2px] bg-[#CBAC70] rounded-full" />
                  )}
                </Link>
              );
            })}
          </nav>

          {/* Right Utilities */}
          <div className="flex items-center gap-3 sm:gap-4">
            
            {/* Search Trigger */}
            <button
              onClick={() => setSearchOpen(true)}
              className="p-2.5 rounded-full text-[#FDFCFF]/80 hover:text-[#CBAC70] hover:bg-[#111D42] transition-colors"
              title="Search Catalog"
            >
              <Search className="w-5 h-5" />
            </button>

            {/* Shopping Bag Button */}
            <button
              onClick={() => setIsCartOpen(true)}
              className="relative p-2.5 rounded-full text-[#FDFCFF] hover:bg-[#111D42] border border-[#CBAC70]/30 hover:border-[#CBAC70] transition-all group active:scale-95"
              aria-label="Open Shopping Bag"
            >
              <ShoppingBag className="w-5 h-5 group-hover:text-[#CBAC70] transition-colors" />
              {cartCount > 0 && (
                <span className="absolute -top-1.5 -right-1.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-[10px] w-5 h-5 rounded-full flex items-center justify-center shadow-lg border border-[#0B132B]">
                  {cartCount}
                </span>
              )}
            </button>

            {/* Mobile Menu Toggle */}
            <button
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 rounded-lg text-[#FDFCFF] hover:bg-[#111D42] transition-colors"
            >
              {mobileMenuOpen ? <X className="w-6 h-6" /> : <Menu className="w-6 h-6" />}
            </button>

          </div>

        </div>

        {/* Mobile Navigation Drawer */}
        {mobileMenuOpen && (
          <div className="lg:hidden bg-[#0B132B] border-b border-[#CBAC70]/30 px-6 py-6 space-y-4 animate-in slide-in-from-top-2">
            <nav className="flex flex-col space-y-3 text-sm font-semibold uppercase tracking-wider text-[#94A3B8]">
              {navLinks.map((link) => (
                <Link
                  key={link.label}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className="py-2 hover:text-[#CBAC70] border-b border-white/5 flex items-center justify-between"
                >
                  <span>{link.label}</span>
                  <ArrowUpRight className="w-4 h-4 text-[#CBAC70]" />
                </Link>
              ))}
            </nav>

            <div className="pt-4 border-t border-[#CBAC70]/20 flex items-center justify-between text-xs text-[#94A3B8]">
              <a 
                href="http://localhost:8000" 
                target="_blank" 
                rel="noopener noreferrer"
                className="text-[#CBAC70] font-bold flex items-center gap-1.5"
              >
                <span>Backoffice Admin Portal</span>
                <ExternalLink className="w-3.5 h-3.5" />
              </a>
            </div>
          </div>
        )}
      </header>

      {/* Global Quick Search Modal */}
      {searchOpen && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-start justify-center pt-24 px-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-2xl max-w-2xl w-full p-6 shadow-2xl space-y-4 animate-in zoom-in-95">
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <span className="text-xs font-bold uppercase tracking-widest text-[#CBAC70]">Search Malega Collection</span>
              <button onClick={() => setSearchOpen(false)} className="text-[#94A3B8] hover:text-white">✕</button>
            </div>

            <form onSubmit={(e) => { e.preventDefault(); if (searchQuery) window.location.href = `/products?search=${encodeURIComponent(searchQuery)}`; }} className="relative">
              <input
                type="text"
                value={searchQuery}
                onChange={(e) => setSearchQuery(e.target.value)}
                placeholder="Cari artikel (misal: Heavyweight Tee, Cargo, Hoodie)..."
                className="w-full bg-[#0B132B] border border-[#CBAC70]/30 rounded-xl px-4 py-3 text-sm text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
                autoFocus
              />
              <button type="submit" className="absolute right-3 top-2.5 px-4 py-1.5 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-lg hover:bg-[#E3CD99]">
                Cari
              </button>
            </form>

            <div className="flex flex-wrap items-center gap-2 pt-2 text-xs text-[#94A3B8]">
              <span>Trending:</span>
              <Link href="/products?search=300GSM" onClick={() => setSearchOpen(false)} className="px-2.5 py-1 rounded bg-[#0B132B] border border-white/10 hover:border-[#CBAC70] text-[#FDFCFF]">
                Boxy 300GSM
              </Link>
              <Link href="/products?search=Hoodie" onClick={() => setSearchOpen(false)} className="px-2.5 py-1 rounded bg-[#0B132B] border border-white/10 hover:border-[#CBAC70] text-[#FDFCFF]">
                French Terry Hoodie
              </Link>
              <Link href="/products?search=Cargo" onClick={() => setSearchOpen(false)} className="px-2.5 py-1 rounded bg-[#0B132B] border border-white/10 hover:border-[#CBAC70] text-[#FDFCFF]">
                Ripstop Cargo
              </Link>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
