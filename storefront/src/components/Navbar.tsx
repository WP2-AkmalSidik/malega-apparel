'use client';

import React, { useState, useRef, useEffect } from 'react';
import Link from 'next/link';
import { usePathname, useRouter } from 'next/navigation';
import { 
  ShoppingBag, 
  Search, 
  Heart, 
  User, 
  LogOut, 
  Sparkles, 
  Trash2, 
  ArrowRight,
  ShieldCheck,
  Copy,
  Check
} from 'lucide-react';
import { useCart } from '../context/CartContext';
import { useWishlist } from '../context/WishlistContext';
import { useAuth } from '../context/AuthContext';
import { useFlyToCart } from '../context/FlyToCartContext';
import BrandLogo from './BrandLogo';
import SearchModal from './SearchModal';
import MobileBottomNav from './MobileBottomNav';

export default function Navbar() {
  const pathname = usePathname();
  const router = useRouter();
  const { cart, cartCount, setIsCartOpen, addToCart } = useCart();
  const { wishlistProducts, wishlistCount, toggleWishlist, isWishlistOpen, setIsWishlistOpen } = useWishlist();
  const { customer, isAuthenticated, logout } = useAuth();
  const { bagBounce, triggerFly } = useFlyToCart();

  const [searchModalOpen, setSearchModalOpen] = useState(false);
  const [userDropdownOpen, setUserDropdownOpen] = useState(false);
  const [wishlistDropdownOpen, setWishlistDropdownOpen] = useState(false);
  const [isBagBouncing, setIsBagBouncing] = useState(false);
  const [showPlusOne, setShowPlusOne] = useState(false);

  // Top Announcement Vouchers Vertical Slide Ticker
  const [vouchers, setVouchers] = useState<any[]>([
    {
      prefix: 'SS26 DROP IS LIVE',
      text: 'Gratis Ongkir se-Indonesia',
      code: 'FREESHIPXTRA',
      tag: 'FREE ONGKIR'
    },
    {
      prefix: 'VIP GOLD ACCESS',
      text: 'Diskon 15% Eksklusif Koleksi Baru',
      code: 'MALEGAVIP15',
      tag: 'DISKON 15%'
    },
    {
      prefix: 'SPECIAL PROMO',
      text: 'Potongan Langsung Rp 50.000',
      code: 'NEWDROP50K',
      tag: 'HEMAT 50K'
    }
  ]);
  const [currentVoucherIndex, setCurrentVoucherIndex] = useState(0);
  const [isTickerPaused, setIsTickerPaused] = useState(false);
  const [copiedVoucher, setCopiedVoucher] = useState<string | null>(null);

  // Load public vouchers from backend API
  useEffect(() => {
    let isMounted = true;
    async function fetchPublicVouchers() {
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
        const res = await fetch(`${apiUrl}/vouchers/public`);
        const json = await res.json();
        if (isMounted && json.success && Array.isArray(json.data) && json.data.length > 0) {
          const mapped = json.data.map((v: any) => ({
            prefix: v.type === 'shipping' ? 'SS26 FREE SHIPPING' : v.type === 'percentage' ? 'VIP GOLD ACCESS' : 'SPECIAL PROMO',
            text: v.description || v.title || v.name,
            code: v.code,
            tag: v.type === 'shipping' ? 'FREE ONGKIR' : v.type === 'percentage' ? 'DISKON' : 'PROMO'
          }));
          setVouchers(mapped);
        }
      } catch (err) {
        // fallback to default static coupons
      }
    }
    fetchPublicVouchers();
    return () => { isMounted = false; };
  }, []);

  // Vertical Carousel Auto-Rotation Timer (Every 4 seconds)
  useEffect(() => {
    if (vouchers.length <= 1 || isTickerPaused) return;

    const interval = setInterval(() => {
      setCurrentVoucherIndex((prev) => (prev + 1) % vouchers.length);
    }, 4000);

    return () => clearInterval(interval);
  }, [vouchers.length, isTickerPaused]);

  const handleCopyVoucher = (e: React.MouseEvent, code: string) => {
    e.stopPropagation();
    navigator.clipboard.writeText(code);
    setCopiedVoucher(code);
    setTimeout(() => setCopiedVoucher(null), 2500);
  };

  // Trigger bounce animation when an item finishes flying to bag
  useEffect(() => {
    if (bagBounce > 0) {
      setIsBagBouncing(true);
      setShowPlusOne(true);
      const timer = setTimeout(() => {
        setIsBagBouncing(false);
      }, 550);
      const plusTimer = setTimeout(() => {
        setShowPlusOne(false);
      }, 900);
      return () => {
        clearTimeout(timer);
        clearTimeout(plusTimer);
      };
    }
  }, [bagBounce]);

  // Global Keyboard Shortcuts (Ctrl+K / Cmd+K to toggle search, Escape to close)
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      // Check for Ctrl+K or Cmd+K
      if ((e.ctrlKey || e.metaKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        setSearchModalOpen((prev) => !prev);
        setUserDropdownOpen(false);
        setWishlistDropdownOpen(false);
      }

      if (e.key === 'Escape') {
        setSearchModalOpen(false);
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
    if (typeof window !== 'undefined' && window.innerWidth < 1024) {
      setIsWishlistOpen(true);
    } else {
      setWishlistDropdownOpen(!wishlistDropdownOpen);
    }
  };

  const handleQuickAddFromWishlist = (product: any, e?: React.MouseEvent) => {
    let startX = typeof window !== 'undefined' ? window.innerWidth / 2 : 200;
    let startY = typeof window !== 'undefined' ? window.innerHeight / 2 : 200;

    if (e && e.currentTarget) {
      const rect = (e.currentTarget as HTMLElement).getBoundingClientRect();
      startX = rect.left + rect.width / 2;
      startY = rect.top + rect.height / 2;
    }

    const img = product.colors[0]?.image || product.gallery[0];
    triggerFly(img, startX, startY);

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
    { label: 'Koleksi', href: '/katalog' },
    { label: 'Lacak Pesanan', href: '/track' },
  ];

  return (
    <>
      {/* Top Luxury Announcement Bar with Vertical Slide Carousel */}
      <div 
        className="bg-[#080E20] border-b border-[#CBAC70]/20 text-[#CBAC70] text-[8.5px] xs:text-[9.5px] sm:text-[10.5px] py-1.5 px-3 sm:px-6 font-medium tracking-wide select-none"
        onMouseEnter={() => setIsTickerPaused(true)}
        onMouseLeave={() => setIsTickerPaused(false)}
      >
        <div className="max-w-7xl mx-auto flex items-center justify-between gap-4">
          
          {/* Vertical Sliding Vouchers Ticker */}
          <div className="relative h-6 flex-1 overflow-hidden min-w-0">
            {vouchers.map((v, idx) => {
              const isCurrent = idx === currentVoucherIndex;
              const isPrev = idx === (currentVoucherIndex - 1 + vouchers.length) % vouchers.length;

              let positionClass = 'translate-y-full opacity-0 pointer-events-none';
              if (isCurrent) {
                positionClass = 'translate-y-0 opacity-100 pointer-events-auto';
              } else if (isPrev) {
                positionClass = '-translate-y-full opacity-0 pointer-events-none';
              }

              return (
                <div
                  key={v.code || idx}
                  className={`absolute inset-0 flex items-center gap-1.5 sm:gap-2 leading-tight transition-all duration-700 ease-[cubic-bezier(0.16,1,0.3,1)] ${positionClass}`}
                >
                  <span className="w-1.5 h-1.5 rounded-full bg-[#CBAC70] animate-pulse shrink-0"></span>
                  <span className="text-[#FDFCFF] font-black tracking-wider shrink-0 text-[8.5px] xs:text-[9.5px] sm:text-[10.5px]">
                    {v.prefix}:
                  </span>
                  <span className="truncate text-[#CBAC70] text-[8.5px] xs:text-[9.5px] sm:text-[10.5px]">
                    {v.text}
                  </span>
                  
                  {v.code && (
                    <button
                      type="button"
                      onClick={(e) => handleCopyVoucher(e, v.code)}
                      className={`ml-1 px-1.5 py-0.5 rounded-md border font-mono text-[8px] sm:text-[9.5px] font-bold tracking-wider transition-all active:scale-95 flex items-center gap-1 cursor-pointer shrink-0 ${
                        copiedVoucher === v.code
                          ? 'bg-emerald-500/20 border-emerald-500/40 text-emerald-400 shadow-sm shadow-emerald-500/20'
                          : 'bg-[#CBAC70]/15 hover:bg-[#CBAC70]/30 border-[#CBAC70]/30 hover:border-[#CBAC70]/60 text-[#CBAC70]'
                      }`}
                      title="Klik untuk salin kode voucher"
                    >
                      {copiedVoucher === v.code ? (
                        <>
                          <Check className="w-2.5 h-2.5 text-emerald-400 stroke-[3]" />
                          <span>TERSALIN</span>
                        </>
                      ) : (
                        <>
                          <span>kode &quot;{v.code}&quot;</span>
                          <Copy className="w-2.5 h-2.5 opacity-70" />
                        </>
                      )}
                    </button>
                  )}
                </div>
              );
            })}
          </div>

          {/* Right: Atelier Badge & Location */}
          <div className="hidden sm:flex items-center gap-3 text-[10.5px] text-[#94A3B8] shrink-0">
            <span className="text-[#CBAC70] font-semibold flex items-center gap-1">
              <Sparkles className="w-3 h-3 text-[#CBAC70]" />
              100% Original Streetwear Atelier
            </span>
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
            
            {/* 1. Instant Search Trigger (Desktop Only) */}
            <button
              type="button"
              onClick={() => setSearchModalOpen(true)}
              className="hidden lg:flex p-2 rounded-xl text-slate-300 hover:text-[#CBAC70] hover:bg-[#14204A] transition items-center gap-1.5 group cursor-pointer"
              title="Pencarian Cepat (Ctrl+K)"
            >
              <Search className="w-4 h-4" />
              <span className="hidden md:inline-block text-[10px] font-mono text-slate-400 bg-white/5 border border-white/10 px-1.5 py-0.5 rounded group-hover:border-[#CBAC70]/40">
                Ctrl+K
              </span>
            </button>

            {/* 2. Wishlist / Favorites Trigger */}
            <div className="relative">
              <button
                type="button"
                onClick={handleHeartClick}
                className={`relative w-8 h-8 sm:w-8.5 sm:h-8.5 rounded-lg sm:rounded-xl transition cursor-pointer flex items-center justify-center group ${
                  wishlistDropdownOpen || isWishlistOpen
                    ? 'bg-[#14204A] text-rose-400 ring-1 ring-rose-500/50'
                    : 'text-slate-300 hover:text-rose-400 hover:bg-[#14204A]'
                }`}
                title="Daftar Favorit"
                aria-label="Daftar Favorit"
              >
                <Heart className={`w-4 h-4 ${wishlistCount > 0 ? 'text-rose-500 fill-rose-500' : ''}`} />
                {wishlistCount > 0 && (
                  <span className="absolute -top-1 -right-1 bg-rose-500 text-white font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center shadow">
                    {wishlistCount}
                  </span>
                )}
              </button>

              {/* Wishlist Dropdown Content (Desktop Only) */}
              {wishlistDropdownOpen && (
                <>
                  {/* Backdrop to close on outside click */}
                  <div className="fixed inset-0 z-40" onClick={() => setWishlistDropdownOpen(false)} />
                  <div 
                    className="hidden lg:block absolute right-0 mt-2 w-80 sm:w-96 rounded-3xl bg-[#0E1736] border border-[#CBAC70]/40 shadow-2xl p-4 z-50 space-y-3 animate-[fadeInScale_0.25s_ease-out_forwards]"
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
                                onClick={(e) => handleQuickAddFromWishlist(p, e)}
                                className="p-1.5 rounded-lg bg-[#CBAC70] text-[#0B132B] hover:bg-[#E3CD99] transition shadow text-xs font-bold cursor-pointer"
                                title="Tambah ke Keranjang"
                                aria-label="Tambah ke Keranjang"
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

            {/* 3. Customer Account / Member Portal Dropdown (Desktop Only) */}
            <div className="hidden lg:block relative">
              {isAuthenticated && customer ? (
                <div>
                  <button
                    type="button"
                    onClick={() => {
                      setWishlistDropdownOpen(false);
                      setUserDropdownOpen(!userDropdownOpen);
                    }}
                    className="h-8 sm:h-8.5 flex items-center gap-1.5 px-2 sm:px-2.5 rounded-lg sm:rounded-xl bg-white/5 hover:bg-white/10 border border-[#CBAC70]/30 transition cursor-pointer"
                  >
                    <div className="w-5 h-5 rounded-md bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-[9px]">
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
                  className="h-8 sm:h-8.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl text-xs font-bold text-slate-200 hover:text-[#CBAC70] bg-white/5 hover:bg-white/10 border border-white/10 transition flex items-center gap-1.5"
                >
                  <User className="w-3.5 h-3.5 text-[#CBAC70]" />
                  <span className="hidden sm:inline">Masuk</span>
                </Link>
              )}
            </div>

            {/* 4. Shopping Bag Drawer Button */}
            <div className="relative">
              <button
                id="navbar-bag-button"
                type="button"
                onClick={handleBagClick}
                className={`relative h-8 sm:h-8.5 px-2.5 sm:px-3 rounded-lg sm:rounded-xl text-[#0B132B] bg-gradient-to-r from-[#CBAC70] via-[#D8BC80] to-[#B89758] hover:from-[#E3CD99] hover:to-[#CBAC70] font-bold text-[11px] sm:text-xs shadow-sm transition-all active:scale-95 flex items-center gap-1.5 cursor-pointer ${
                  isBagBouncing ? 'animate-bag-pop ring-2 ring-[#E3CD99]' : ''
                }`}
                aria-label="Buka Keranjang Belanja"
              >
                <ShoppingBag className={`w-3.5 h-3.5 transition-transform ${isBagBouncing ? 'scale-110' : ''}`} />
                <span className="hidden sm:inline font-bold">Keranjang</span>
                {cartCount > 0 && (
                  <span className={`bg-[#0B132B] text-[#CBAC70] text-[9.5px] font-black px-1.5 min-w-[17px] h-4 rounded-full font-mono flex items-center justify-center leading-none transition-transform ${
                    isBagBouncing ? 'animate-badge-bump ring-1 ring-[#E3CD99]' : ''
                  }`}>
                    {cartCount}
                  </span>
                )}
              </button>

              {/* Luxury Floating +1 Notification Particle */}
              {showPlusOne && (
                <div className="absolute -top-2.5 right-0 transform translate-x-1 -translate-y-1 pointer-events-none z-50 animate-bounce">
                  <span className="bg-gradient-to-r from-[#E3CD99] to-[#CBAC70] text-[#0B132B] text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-[0_0_8px_#CBAC70] border border-white/20">
                    +1
                  </span>
                </div>
              )}
            </div>

          </div>
        </div>
      </header>

      {/* Global Instant Search Modal */}
      <SearchModal
        isOpen={searchModalOpen}
        onClose={() => setSearchModalOpen(false)}
      />

      {/* Luxury Mobile App Bottom Navigation Bar */}
      <MobileBottomNav
        onOpenSearch={() => setSearchModalOpen(true)}
        isSearchOpen={searchModalOpen}
      />
    </>
  );
}
