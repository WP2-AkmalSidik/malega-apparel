'use client';

import React, { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import { 
  Search, 
  X, 
  ArrowRight, 
  Sparkles, 
  Tag, 
  Layers, 
  Check, 
  Loader2, 
  Flame, 
  TrendingUp,
  Compass,
  Truck
} from 'lucide-react';
import { productsCatalog } from '../data/products';
import { Product } from '../types';

interface SearchModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function SearchModal({ isOpen, onClose }: SearchModalProps) {
  const [query, setQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  
  // Curated initial recommendations (Top 4 items only)
  const [featuredProducts, setFeaturedProducts] = useState<Product[]>(() => productsCatalog.slice(0, 4));
  // Dynamic search results (Max 8 items when searching)
  const [searchResults, setSearchResults] = useState<Product[]>([]);
  const [totalMatches, setTotalMatches] = useState<number>(0);
  const [isLoading, setIsLoading] = useState(false);
  const inputRef = useRef<HTMLInputElement>(null);

  // Focus input when modal opens & reset query
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        inputRef.current?.focus();
      }, 100);
    } else {
      setQuery('');
      setSelectedCategory('all');
      setSearchResults([]);
    }
  }, [isOpen]);

  // Global Keyboard shortcut: Escape or Ctrl+K/Cmd+K to close
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && (e.key === 'k' || e.key === 'K')) {
        e.preventDefault();
        if (isOpen) {
          onClose();
        }
      }
      if (e.key === 'Escape' && isOpen) {
        onClose();
      }
    };

    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isOpen, onClose]);

  // Load Initial Curated Recommendations (Top 4 items from DB) when opened
  useEffect(() => {
    if (!isOpen) return;

    let isMounted = true;
    async function loadCuratedTopItems() {
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
        const res = await fetch(`${apiUrl}/products?sort=popular&limit=4`);
        const json = await res.json();
        if (isMounted && json.success && Array.isArray(json.data) && json.data.length > 0) {
          const mapped: Product[] = json.data.slice(0, 4).map((item: any) => mapApiProduct(item));
          setFeaturedProducts(mapped);
        }
      } catch (e) {
        // fallback to top 4 catalog items
        setFeaturedProducts(productsCatalog.slice(0, 4));
      }
    }

    loadCuratedTopItems();

    return () => {
      isMounted = false;
    };
  }, [isOpen]);

  // Search execution with Debounce (180ms) when query or category changes
  useEffect(() => {
    if (!isOpen) return;

    const trimmed = query.trim();
    if (!trimmed && selectedCategory === 'all') {
      setSearchResults([]);
      setTotalMatches(0);
      setIsLoading(false);
      return;
    }

    let isMounted = true;
    setIsLoading(true);

    const timer = setTimeout(async () => {
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
        const params = new URLSearchParams();
        if (trimmed) params.set('search', trimmed);
        if (selectedCategory !== 'all') params.set('category', selectedCategory);
        params.set('limit', '8');

        const res = await fetch(`${apiUrl}/products?${params.toString()}`);
        const json = await res.json();

        if (isMounted && json.success && Array.isArray(json.data)) {
          const mapped: Product[] = json.data.map((item: any) => mapApiProduct(item));
          setSearchResults(mapped);
          setTotalMatches(json.meta?.total || mapped.length);
        }
      } catch (err) {
        // Fallback local search
        const local = productsCatalog.filter(p => {
          if (selectedCategory !== 'all') {
            const catNorm = selectedCategory.toLowerCase().replace('-', '');
            const prodCatNorm = p.category.toLowerCase().replace('-', '');
            if (!prodCatNorm.includes(catNorm) && !catNorm.includes(prodCatNorm)) {
              return false;
            }
          }
          if (!trimmed) return true;
          const q = trimmed.toLowerCase();
          return (
            p.title.toLowerCase().includes(q) ||
            p.subtitle.toLowerCase().includes(q) ||
            p.material.toLowerCase().includes(q) ||
            p.category.toLowerCase().includes(q) ||
            `${p.gsm}gsm`.includes(q) ||
            p.description.toLowerCase().includes(q)
          );
        });
        setSearchResults(local.slice(0, 8));
        setTotalMatches(local.length);
      } finally {
        if (isMounted) setIsLoading(false);
      }
    }, 180);

    return () => {
      isMounted = false;
      clearTimeout(timer);
    };
  }, [isOpen, query, selectedCategory]);

  // Helper mapper for API response to Product type
  const mapApiProduct = (item: any): Product => {
    const defaultImg = item.featured_image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
    const colors = (Array.isArray(item.colors) && item.colors.length > 0)
      ? item.colors
      : [{ name: 'Signature', hex: '#0B132B', image: defaultImg }];
    const sizes = (Array.isArray(item.sizes) && item.sizes.length > 0)
      ? item.sizes
      : ['All Size'];

    return {
      id: String(item.id),
      slug: item.slug,
      title: item.name || item.title,
      subtitle: item.subtitle || '',
      badge: item.badge,
      isNewDrop: item.badge?.includes('NEW') || item.badge?.includes('DROP') || Number(item.id) >= 10,
      isBestSeller: item.badge?.includes('BEST') || item.badge?.includes('TOP') || (item.sold_count && item.sold_count > 1000),
      rating: item.rating || 4.9,
      reviewCount: item.review_count || 120,
      soldCount: item.sold_count || 500,
      originalPrice: item.price?.compare_at || item.price?.max || item.price?.min || 0,
      price: item.price?.min || item.price || 0,
      priceMin: item.price?.min,
      priceMax: item.price?.max,
      discountPercentage: item.price?.discount_percentage || 0,
      category: item.category?.name || 'Streetwear',
      material: item.material || item.specifications?.Material || 'Cotton Heavyweight',
      gsm: item.gsm || (item.specifications?.Gramasi ? parseInt(item.specifications.Gramasi) : 300),
      fit: item.fit || item.specifications?.Cutting || item.specifications?.['Fit / Cutting'] || 'Boxy Oversized',
      origin: 'Bandung, Indonesia',
      stockTotal: item.variants_count ? item.variants_count * 10 : 50,
      colors,
      sizes,
      gallery: [defaultImg],
      features: ['100% Original Malega Streetwear', 'Garansi Kepuasan & Retur Mudah'],
      description: item.description || '',
      specifications: item.specifications || {},
      variants: item.variants || []
    };
  };

  const trendingTags = [
    '300GSM',
    'French Terry',
    'Tactical Cargo',
    'Gold Monogram Cap',
    'Raw Denim',
    'Drop Shoulder'
  ];

  const categoryOptions = [
    { label: 'Semua', value: 'all' },
    { label: 'T-Shirts', value: 't-shirts' },
    { label: 'Outerwear', value: 'outerwear' },
    { label: 'Bottoms', value: 'bottoms' },
    { label: 'Accessories', value: 'accessories' }
  ];

  const isSearchingActive = query.trim().length > 0 || selectedCategory !== 'all';

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center pt-12 sm:pt-20 px-3 sm:px-4 bg-black/80 backdrop-blur-md transition-opacity">
      {/* Backdrop click to close */}
      <div className="fixed inset-0" onClick={onClose} />

      {/* Search Dialog Box */}
      <div className="relative w-full max-w-2xl bg-[#0E1736] border border-[#CBAC70]/40 rounded-3xl shadow-2xl overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-200">
        
        {/* Search Input Bar */}
        <div className="p-3.5 sm:p-4 border-b border-white/10 bg-[#070D1F]/90 flex items-center gap-3">
          {isLoading ? (
            <Loader2 className="w-5 h-5 text-[#CBAC70] animate-spin shrink-0" />
          ) : (
            <Search className="w-5 h-5 text-[#CBAC70] shrink-0" />
          )}
          <input
            ref={inputRef}
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Cari kaos 300GSM, hoodie, cargo, kemeja, atau aksesori..."
            className="w-full bg-transparent text-xs sm:text-sm text-white placeholder-slate-400 focus:outline-none"
          />
          {query && (
            <button
              onClick={() => setQuery('')}
              className="p-1 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition cursor-pointer"
              title="Hapus kata kunci"
            >
              <X className="w-4 h-4" />
            </button>
          )}
          <button
            onClick={onClose}
            className="px-2 py-0.5 text-[10px] font-mono font-semibold text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-md border border-white/10 transition cursor-pointer"
          >
            ESC
          </button>
        </div>

        {/* Category Filter Pills */}
        <div className="px-4 py-2.5 bg-[#0B132B]/90 border-b border-white/5 flex items-center justify-between gap-2 overflow-x-auto scrollbar-none">
          <div className="flex items-center gap-1.5 shrink-0">
            {categoryOptions.map(cat => (
              <button
                key={cat.value}
                onClick={() => setSelectedCategory(cat.value)}
                className={`px-3 py-1 rounded-xl text-xs font-medium transition active:scale-95 cursor-pointer ${
                  selectedCategory === cat.value
                    ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                    : 'bg-white/5 hover:bg-white/10 text-slate-300'
                }`}
              >
                {cat.label}
              </button>
            ))}
          </div>

          <span className="text-[10px] font-mono text-emerald-400 shrink-0 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-full flex items-center gap-1">
            <span className="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse" />
            <span>Database API Aktif</span>
          </span>
        </div>

        {/* Content Section: Switch between Initial Curated View vs Search Results */}
        {!isSearchingActive ? (
          /* Initial Clean State: Trending Tags + Top 4 Curated Recommendations */
          <div className="max-h-[60vh] overflow-y-auto divide-y divide-white/5 p-4 space-y-4">
            
            {/* 1. Trending Keywords */}
            <div className="space-y-2">
              <p className="text-[11px] font-mono uppercase tracking-wider text-[#CBAC70] flex items-center gap-1.5">
                <Flame className="w-3.5 h-3.5 text-[#CBAC70]" />
                <span>Pencarian Populer Musim SS26</span>
              </p>
              <div className="flex items-center gap-1.5 flex-wrap">
                {trendingTags.map(tag => (
                  <button
                    key={tag}
                    onClick={() => setQuery(tag)}
                    className="px-2.5 py-1 rounded-lg text-xs bg-[#14204A]/60 hover:bg-[#CBAC70]/20 text-slate-300 hover:text-[#CBAC70] border border-white/5 hover:border-[#CBAC70]/40 transition cursor-pointer active:scale-95"
                  >
                    #{tag}
                  </button>
                ))}
              </div>
            </div>

            {/* 2. Top Curated Recommendations (Max 4 items) */}
            <div className="pt-4 space-y-2.5">
              <div className="flex items-center justify-between">
                <p className="text-[11px] font-mono uppercase tracking-wider text-slate-300 flex items-center gap-1.5">
                  <TrendingUp className="w-3.5 h-3.5 text-emerald-400" />
                  <span>Rekomendasi Terpopuler ({featuredProducts.length} Artikel)</span>
                </p>
                <Link
                  href="/katalog"
                  onClick={onClose}
                  className="text-[11px] font-mono text-[#CBAC70] hover:underline flex items-center gap-1"
                >
                  <span>Lihat Semua</span>
                  <ArrowRight className="w-3 h-3" />
                </Link>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                {featuredProducts.map(product => (
                  <Link
                    key={product.id}
                    href={`/products/${product.slug}`}
                    onClick={onClose}
                    className="p-2.5 rounded-2xl bg-[#080E20]/80 hover:bg-white/5 border border-white/5 hover:border-[#CBAC70]/40 transition flex items-center gap-3 group"
                  >
                    <img
                      src={product.colors[0]?.image || product.gallery[0]}
                      alt={product.title}
                      className="w-12 h-12 rounded-xl object-cover border border-white/10 bg-[#070D1F] shrink-0 group-hover:border-[#CBAC70]/50 transition"
                    />
                    <div className="min-w-0 flex-1">
                      <p className="font-bold text-xs text-slate-100 group-hover:text-[#CBAC70] transition truncate">
                        {product.title}
                      </p>
                      <div className="flex items-center justify-between gap-1 mt-0.5">
                        <span className="font-bold font-mono text-xs text-[#CBAC70]">
                          Rp {product.price.toLocaleString('id-ID')}
                        </span>
                        {product.gsm && (
                          <span className="text-[9px] font-mono px-1 py-0.2 rounded bg-white/5 text-slate-400">
                            {product.gsm}GSM
                          </span>
                        )}
                      </div>
                    </div>
                  </Link>
                ))}
              </div>
            </div>

            {/* 3. Quick Navigation Links */}
            <div className="pt-4 flex items-center justify-between gap-2 text-xs text-slate-400">
              <Link
                href="/katalog"
                onClick={onClose}
                className="flex-1 p-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 flex items-center justify-center gap-1.5 transition text-slate-300 hover:text-[#CBAC70]"
              >
                <Compass className="w-3.5 h-3.5 text-[#CBAC70]" />
                <span>Lookbook Koleksi</span>
              </Link>

              <Link
                href="/track"
                onClick={onClose}
                className="flex-1 p-2 rounded-xl bg-white/5 hover:bg-white/10 border border-white/5 flex items-center justify-center gap-1.5 transition text-slate-300 hover:text-[#CBAC70]"
              >
                <Truck className="w-3.5 h-3.5 text-sky-400" />
                <span>Lacak Pesanan</span>
              </Link>
            </div>

          </div>
        ) : (
          /* Active Search Results State (Max 8 items) */
          <div className="max-h-[60vh] overflow-y-auto divide-y divide-white/5 p-2">
            {isLoading ? (
              <div className="py-12 text-center text-slate-400 space-y-2">
                <Loader2 className="w-6 h-6 text-[#CBAC70] animate-spin mx-auto" />
                <p className="text-xs text-slate-400">Mencari artikel pada database...</p>
              </div>
            ) : searchResults.length > 0 ? (
              <>
                <div className="px-3 py-1.5 text-[11px] font-mono text-slate-400 flex items-center justify-between">
                  <span>Hasil pencarian: {searchResults.length} dari {totalMatches} produk</span>
                  {selectedCategory !== 'all' && (
                    <span className="text-[#CBAC70]">Kategori: {selectedCategory}</span>
                  )}
                </div>

                {searchResults.map(product => (
                  <Link
                    key={product.id}
                    href={`/products/${product.slug}`}
                    onClick={onClose}
                    className="p-3 rounded-2xl hover:bg-white/5 transition flex items-center justify-between gap-3 group"
                  >
                    <div className="flex items-center gap-3 min-w-0">
                      <img
                        src={product.colors[0]?.image || product.gallery[0]}
                        alt={product.title}
                        className="w-13 h-13 rounded-xl object-cover border border-white/10 bg-[#070D1F] shrink-0 group-hover:border-[#CBAC70]/50 transition"
                      />
                      <div className="min-w-0">
                        <div className="flex items-center gap-2">
                          <span className="text-[10px] font-mono px-1.5 py-0.5 rounded bg-white/5 text-slate-300">
                            {product.category}
                          </span>
                          {product.gsm && (
                            <span className="text-[10px] font-mono px-1.5 py-0.5 rounded bg-[#CBAC70]/15 text-[#CBAC70]">
                              {product.gsm}GSM
                            </span>
                          )}
                        </div>
                        <p className="font-bold text-xs sm:text-sm text-slate-100 group-hover:text-[#CBAC70] transition truncate mt-0.5">
                          {product.title}
                        </p>
                        <p className="text-[11px] text-slate-400 truncate max-w-sm">
                          {product.subtitle || product.material}
                        </p>
                      </div>
                    </div>

                    <div className="text-right shrink-0">
                      <p className="font-bold font-mono text-xs sm:text-sm text-[#CBAC70]">
                        Rp {product.price.toLocaleString('id-ID')}
                      </p>
                      {product.originalPrice > product.price && (
                        <p className="text-[10px] font-mono text-slate-500 line-through">
                          Rp {product.originalPrice.toLocaleString('id-ID')}
                        </p>
                      )}
                      <span className="text-[10px] text-emerald-400 font-mono block">
                        ✓ Ready Stock
                      </span>
                    </div>
                  </Link>
                ))}
              </>
            ) : (
              <div className="py-12 text-center text-slate-400 space-y-2">
                <p className="text-sm font-semibold text-slate-300">
                  Tidak ada produk yang cocok dengan &quot;{query}&quot;
                </p>
                <p className="text-xs text-slate-500">
                  Coba kata kunci lain seperti 300GSM, Hoodie, Cargo, atau Monogram Cap.
                </p>
              </div>
            )}
          </div>
        )}

        {/* Footer Navigation Bar */}
        <div className="p-3 bg-[#070D1F] border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400 font-mono">
          <div className="flex items-center gap-3">
            <span className="hidden sm:inline-block">ESC untuk Tutup</span>
            <span className="hidden sm:inline-block">•</span>
            <span>↵ untuk Buka</span>
          </div>

          <Link
            href={query.trim() ? `/katalog?search=${encodeURIComponent(query.trim())}` : '/katalog'}
            onClick={onClose}
            className="text-[#CBAC70] hover:underline flex items-center gap-1 font-semibold"
          >
            <span>{query.trim() ? `Lihat Semua Hasil (${totalMatches})` : 'Buka Semua Katalog'}</span>
            <ArrowRight className="w-3 h-3" />
          </Link>
        </div>

      </div>
    </div>
  );
}
