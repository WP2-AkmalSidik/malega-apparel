'use client';

import React, { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { 
  ShoppingBag, 
  Search, 
  Menu, 
  X, 
  Heart, 
  User, 
  LogOut, 
  Sparkles, 
  Trash2, 
  ArrowRight,
  ShieldCheck
} from 'lucide-react';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';
import { useAuth } from '../context/AuthContext';
import BrandLogo from './BrandLogo';
import SearchModal from './SearchModal';

export default function Navbar() {
  const pathname = usePathname();
  const router = useRouter();
  const { cart, cartCount, setIsCartOpen, addToCart } = useCart();
  const { wishlistProducts, wishlistCount, toggleWishlist } = useWishlist();
  const { customer, isAuthenticated, logout } = useAuth();

  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
  const [searchModalOpen, setSearchModalOpen] = useState(false);
  const [userDropdownOpen, setUserDropdownOpen] = useState(false);
  const [wishlistDropdownOpen, setWishlistDropdownOpen] = useState(false);

  // Close dropdowns on outside click or escape
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape') {
        setUserDropdownOpen(false);
        setWishlistDropdownOpen(false);
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, []);

  const handleBagClick = () => {
    setUserDropdownOpen(false);
    setWishlistDropdownOpen(false);
    setIsCartOpen(true);
  };

  const handleHeartClick = () => {
    setUserDropdownOpen(false);
    setWishlistDropdownOpen(!wishlistDropdownOpen);
  };

  const handleQuickAddFromWishlist = (product: any) => {
    addToCart({
      productId: product.id,
      slug: product.slug,
      title: product.title,
      color: product.colors[0]?.name || 'Standard',
      size: product.sizes[0] || 'L',
      price: product.price,
      originalPrice: product.originalPrice,
      quantity: 1,
      image: product.colors[0]?.image || product.gallery[0]
    });
  };

  const navLinks = [
    { label: 'Home', href: '/' },
    { label: 'Lookbook Koleksi', href: '/katalog' },
    { label: 'Semua Produk', href: '/products' },
    { label: 'Lacak Pesanan', href: '/track' },
  ];

  return (
    <>
      {/* Top Luxury Announcement Bar */}
      <div className="bg-[#080E20] border-b border-[#CBAC70]/20 text-[#CBAC70] text-[8.5px] xs:text-[9.5px] sm:text-[10.5px] py-1 px-3 sm:px-6 font-medium tracking-wide">
        <div className="max-w-7xl mx-auto flex items-center justify-between">
          <div className="flex items-center gap-1.5 sm:gap-2 leading-tight overflow-hidden">
            <span className="w-1.5 h-1.5 rounded-full bg-[#CBAC70] animate-pulse shrink-0"></span>
            <span className="text-[#FDFCFF] font-bold tracking-wider shrink-0">SS26 DROP IS LIVE:</span>
            <span className="truncate text-[#CBAC70]">Gratis Ongkir se-Indonesia kode &quot;FREESHIPXTRA&quot;</span>
          </div>

          <div className="hidden sm:flex items-center gap-3 text-[10.5px] text-[#94A3B8]">
            <span className="text-[#CBAC70] font-semibold">100% Original Streetwear Atelier</span>
            <span>•</span>
            <span>Bandung, ID</span>
          </div>
        </div>
      </div>

      {/* Main Luxury Header */}
      <header className="sticky top-0 z-40 bg-[#0B132B]/95 backdrop-blur-xl border-b border-[#CBAC70]/20">
        <div className="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 h-14 sm:h-16 flex items-center justify-between gap-4">
          
          {/* Left: Brand Logo */}
          <div className="flex items-center">
            <BrandLogo size="md" />
          </div>

          {/* Center: Desktop Navigation Links with Clear Labels */}
          <nav className="hidden lg:flex items-center gap-7 text-xs font-bold uppercase tracking-[0.18em] text-[#94A3B8]">
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

          {/* Right Utilities (Search, Wishlist, User, Bag) */}
          <div className="flex items-center gap-1.5 sm:gap-2.5">
            
            {/* 1. Instant Search Trigger */}
            <button
              type="button"
              onClick={() => setSearchModalOpen(true)}
              className="p-2 rounded-xl text-slate-300 hover:text-[#CBAC70] hover:bg-[#14204A] transition flex items-center gap-1.5 group cursor-pointer"
              title="Pencarian Cepat (Ctrl+K)"
            >
              <Search className="w-4 h-4" />
              <span className="hidden md:inline-block text-[10px] font-mono text-slate-400 bg-white/5 border border-white/10 px-1.5 py-0.5 rounded group-hover:border-[#CBAC70]/40">
                Ctrl+K
              </span>
            </button>

            {/* 2. Wishlist / Favorites Dropdown Trigger */}
            <div className="relative">
              <button
                type="button"
                onClick={handleHeartClick}
                className={`relative p-2 rounded-xl transition cursor-pointer group ${
                  wishlistDropdownOpen
                    ? 'bg-[#14204A] text-rose-400 ring-1 ring-rose-500/50'
                    : 'text-slate-300 hover:text-rose-400 hover:bg-[#14204A]'
                }`}
                title="Daftar Favorit Tersimpan (Cache)"
              >
                <Heart className={`w-4 h-4 ${wishlistCount > 0 ? 'text-rose-500 fill-rose-500' : ''}`} />
                {wishlistCount > 0 && (
                  <span className="absolute -top-1 -right-1 bg-rose-500 text-white font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center shadow">
                    {wishlistCount}
                  </span>
                )}
              </button>

              {/* Wishlist Dropdown Content */}
              {wishlistDropdownOpen && (
                <>
                  {/* Backdrop to close on outside click */}
                  <div className="fixed inset-0 z-40" onClick={() => setWishlistDropdownOpen(false)} />
                  <div 
                    className="absolute right-0 mt-2 w-80 sm:w-96 rounded-3xl bg-[#0E1736] border border-[#CBAC70]/40 shadow-2xl p-4 z-50 space-y-3 animate-[fadeInScale_0.25s_ease-out_forwards]"
                  >
                  <div className="flex items-center justify-between pb-2 border-b border-white/10">
                    <div className="flex items-center gap-2">
                      <Heart className="w-4 h-4 text-rose-500 fill-rose-500" />
                      <h4 className="text-xs font-bold text-white uppercase tracking-wider">
                        Favorit Saya ({wishlistCount})
                      </h4>
                    </div>
                    <span className="text-[10px] font-mono text-[#CBAC70] bg-[#CBAC70]/10 px-2 py-0.5 rounded-full border border-[#CBAC70]/30">
                      Tersimpan di Cache
                    </span>
                  </div>

                  {/* List of Wishlist Products */}
                  <div className="max-h-64 overflow-y-auto space-y-2.5 pr-1 divide-y divide-white/5">
                    {wishlistProducts.length > 0 ? (
                      wishlistProducts.map(p => (
                        <div key={p.id} className="pt-2 flex items-center justify-between gap-3">
                          <img
                            src={p.colors[0]?.image || p.gallery[0]}
                            alt={p.title}
                            className="w-12 h-12 rounded-xl object-cover border border-white/10 bg-[#070D1F] shrink-0"
                          />
                          <div className="min-w-0 flex-1">
                            <Link
                              href={`/products/${p.slug}`}
                              onClick={() => setWishlistDropdownOpen(false)}
                              className="text-xs font-bold text-slate-100 hover:text-[#CBAC70] transition line-clamp-1 block"
                            >
                              {p.title}
                            </Link>
                            <p className="text-[11px] font-mono font-bold text-[#CBAC70]">
                              Rp {p.price.toLocaleString('id-ID')}
                            </p>
                          </div>

                          <div className="flex items-center gap-1.5 shrink-0">
                            <button
                              type="button"
                              onClick={() => handleQuickAddFromWishlist(p)}
                              className="p-1.5 rounded-lg bg-[#CBAC70] text-[#0B132B] hover:bg-[#E3CD99] transition shadow text-xs font-bold cursor-pointer"
                              title="Beli / Tambah ke Bag"
                            >
                              <ShoppingBag className="w-3.5 h-3.5" />
                            </button>
                            <button
                              type="button"
                              onClick={() => toggleWishlist(p.id)}
                              className="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-white/5 transition cursor-pointer"
                              title="Hapus dari Favorit"
                            >
                              <Trash2 className="w-3.5 h-3.5" />
                            </button>
                          </div>
                        </div>
                      ))
                    ) : (
                      <div className="py-8 text-center text-slate-400 space-y-1">
                        <p className="text-xs font-semibold text-slate-300">Belum ada produk favorit</p>
                        <p className="text-[11px] text-slate-500">
                          Klik tombol hati pada produk untuk menyimpannya di sini tanpa perlu login.
                        </p>
                      </div>
                    )}
                  </div>

                  {/* Footer Link */}
                  {wishlistProducts.length > 0 && (
                    <div className="pt-2 border-t border-white/10 flex items-center justify-between text-xs">
                      <Link
                        href="/favorites"
                        onClick={() => setWishlistDropdownOpen(false)}
                        className="text-[#CBAC70] hover:underline font-semibold flex items-center gap-1"
                      >
                        <span>Buka Halaman Favorit Penuh</span>
                        <ArrowRight className="w-3.5 h-3.5" />
                      </Link>
                    </div>
                  )}
                </div>
                </>
              )}
            </div>

            {/* 3. Customer Account / Member Portal Dropdown */}
            <div className="relative">
              {isAuthenticated && customer ? (
                <div>
                  <button
                    type="button"
                    onClick={() => {
                      setWishlistDropdownOpen(false);
                      setUserDropdownOpen(!userDropdownOpen);
                    }}
                    className="flex items-center gap-2 p-1 sm:px-2.5 sm:py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-[#CBAC70]/30 transition cursor-pointer"
                  >
                    <div className="w-6 h-6 rounded-lg bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-[10px]">
                      {customer.name.substring(0, 1).toUpperCase()}
                    </div>
                    <span className="hidden sm:inline-block text-xs font-bold text-slate-200 truncate max-w-[90px]">
                      {customer.name.split(' ')[0]}
                    </span>
                  </button>

                  {/* Dropdown Menu Logged-In */}
                  {userDropdownOpen && (
                    <div className="absolute right-0 mt-2 w-56 rounded-2xl bg-[#0E1736] border border-[#CBAC70]/30 shadow-2xl p-2 z-50 animate-in fade-in zoom-in-95 space-y-1">
                      <div className="p-2 border-b border-white/5 space-y-0.5">
                        <p className="text-xs font-bold text-white truncate">{customer.name}</p>
                        <span className="text-[10px] font-mono text-[#CBAC70] block">★ {customer.membership_tier} Member</span>
                      </div>

                      <Link
                        href="/account"
                        onClick={() => setUserDropdownOpen(false)}
                        className="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition"
                      >
                        <User className="w-3.5 h-3.5 text-[#CBAC70]" />
                        <span>Dashboard Akun</span>
                      </Link>

                      <Link
                        href="/favorites"
                        onClick={() => setUserDropdownOpen(false)}
                        className="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition"
                      >
                        <Heart className="w-3.5 h-3.5 text-rose-400" />
                        <span>Wishlist ({wishlistCount})</span>
                      </Link>

                      <Link
                        href="/track"
                        onClick={() => setUserDropdownOpen(false)}
                        className="flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:text-white hover:bg-white/5 transition"
                      >
                        <Sparkles className="w-3.5 h-3.5 text-amber-400" />
                        <span>Lacak Pesanan</span>
                      </Link>

                      <button
                        type="button"
                        onClick={() => { logout(); setUserDropdownOpen(false); }}
                        className="w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition text-left cursor-pointer"
                      >
                        <LogOut className="w-3.5 h-3.5" />
                        <span>Keluar (Logout)</span>
                      </button>
                    </div>
                  )}
                </div>
              ) : (
                <Link
                  href="/login"
                  className="p-2 sm:px-3 sm:py-1.5 rounded-xl text-xs font-bold text-slate-200 hover:text-[#CBAC70] bg-white/5 hover:bg-white/10 border border-white/10 transition flex items-center gap-1.5"
                >
                  <User className="w-4 h-4 text-[#CBAC70]" />
                  <span className="hidden sm:inline">Masuk</span>
                </Link>
              )}
            </div>

            {/* 4. Shopping Bag Drawer Button */}
            <div className="relative">
              <button
                type="button"
                onClick={handleBagClick}
                className="relative p-2 sm:px-3 sm:py-1.5 rounded-xl text-[#0B132B] bg-gradient-to-r from-[#CBAC70] to-[#A58645] hover:from-[#E3CD99] hover:to-[#CBAC70] font-bold text-xs shadow-md transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer"
                aria-label="Open Shopping Bag"
              >
                <ShoppingBag className="w-4 h-4" />
                <span className="hidden sm:inline">Bag</span>
                {cartCount > 0 && (
                  <span className="bg-[#0B132B] text-[#CBAC70] text-[10px] px-1.5 py-0.2 rounded-full font-mono">
                    {cartCount}
                  </span>
                )}
              </button>
            </div>

            {/* Mobile Menu Toggle Button */}
            <button
              type="button"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
              className="lg:hidden p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 cursor-pointer"
            >
              {mobileMenuOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
            </button>

          </div>
        </div>

        {/* Mobile Slide-Down Menu */}
        {mobileMenuOpen && (
          <div className="lg:hidden border-t border-[#CBAC70]/20 bg-[#0B132B] p-4 space-y-3">
            <div className="space-y-1">
              {navLinks.map((link) => (
                <Link
                  key={link.label}
                  href={link.href}
                  onClick={() => setMobileMenuOpen(false)}
                  className={`block px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider ${
                    pathname === link.href ? 'bg-[#14204A] text-[#CBAC70]' : 'text-slate-300 hover:bg-white/5'
                  }`}
                >
                  {link.label}
                </Link>
              ))}
              <Link
                href="/favorites"
                onClick={() => setMobileMenuOpen(false)}
                className="flex items-center justify-between px-4 py-2.5 rounded-xl text-xs font-bold text-slate-300 hover:bg-white/5"
              >
                <span>Wishlist / Favorit</span>
                <span className="text-[#CBAC70] font-mono">{wishlistCount} item</span>
              </Link>
              <Link
                href="/account"
                onClick={() => setMobileMenuOpen(false)}
                className="block px-4 py-2.5 rounded-xl text-xs font-bold text-[#CBAC70] bg-[#14204A]/60"
              >
                {isAuthenticated ? `Akun Saya (${customer?.name})` : 'Masuk / Daftar Anggota'}
              </Link>
            </div>
          </div>
        )}
      </header>

      {/* Global Instant Search Modal */}
      <SearchModal
        isOpen={searchModalOpen}
        onClose={() => setSearchModalOpen(false)}
      />
    </>
  );
}
