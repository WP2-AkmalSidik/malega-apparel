'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  ArrowLeft, 
  Sparkles, 
  Layers, 
  Search, 
  X,
  ArrowUpDown,
  ShoppingBag,
  CheckCircle2
} from 'lucide-react';
import { katalogCollections } from '../../../data/katalog';
import { productsCatalog } from '../../../data/products';
import ProductCard from '../../../components/ProductCard';

interface KatalogDetailClientProps {
  slug: string;
}

export default function KatalogDetailClient({ slug }: KatalogDetailClientProps) {
  const katalog = katalogCollections.find(c => c.slug === slug || c.id === slug) || katalogCollections[0];

  const [searchQuery, setSearchQuery] = useState<string>('');
  const [sortBy, setSortBy] = useState<string>('featured');

  // Filter products that belong to this catalog
  const catalogProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      const isInIdList = (katalog.productIds || []).includes(p.id);
      const isMatchingCategory = (katalog.slug.includes('tee') && p.category === 'T-Shirts') ||
        (katalog.slug.includes('outerwear') && p.category === 'Outerwear') ||
        (katalog.slug.includes('bottoms') && p.category === 'Bottoms') ||
        (katalog.slug.includes('accessories') && p.category === 'Accessories') ||
        (katalog.slug.includes('capsule') && p.isNewDrop) ||
        (katalog.slug.includes('bestseller') && p.isBestSeller);

      if (!isInIdList && !isMatchingCategory) return false;

      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = p.title.toLowerCase().includes(q);
        const matchDesc = p.description.toLowerCase().includes(q);
        if (!matchTitle && !matchDesc) return false;
      }
      return true;
    }).sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'sold') return b.soldCount - a.soldCount;
      if (sortBy === 'rating') return b.rating - a.rating;
      return 0;
    });
  }, [katalog, searchQuery, sortBy]);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6">
      
      {/* 1. Back to Directory Bar */}
      <div className="flex items-center justify-between text-xs">
        <Link
          href="/katalog"
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-[#CBAC70] font-bold transition"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          <span>Kembali ke Direktori Lookbook</span>
        </Link>

        <span className="text-[11px] font-mono text-slate-400">
          Koleksi Musim {katalog.season} {katalog.releaseYear}
        </span>
      </div>

      {/* 2. Hero Lookbook Storytelling Card */}
      <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-6 sm:p-8 border border-[#CBAC70]/30 shadow-2xl space-y-4">
        <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-white/10 pb-4">
          <div className="space-y-1">
            <span className="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-[#CBAC70] text-[#0B132B]">
              {katalog.badge}
            </span>
            <h1 className="text-2xl sm:text-3xl font-black text-white uppercase tracking-wide">
              {katalog.title}
            </h1>
            <p className="text-xs sm:text-sm text-[#CBAC70] font-semibold">
              {katalog.subtitle}
            </p>
          </div>

          <div className="flex items-center gap-2">
            {katalog.palette.map((color, idx) => (
              <span
                key={idx}
                className="w-5 h-5 rounded-full border border-white/20 shadow"
                style={{ backgroundColor: color }}
                title={`Palet ${idx + 1}`}
              />
            ))}
          </div>
        </div>

        <p className="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-3xl">
          {katalog.storytelling}
        </p>
      </div>

      {/* 3. Product Grid belonging to this Catalog */}
      <div className="space-y-4">
        <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-white/10 pb-3">
          <h2 className="text-sm sm:text-base font-bold text-white uppercase tracking-wider flex items-center gap-2">
            <ShoppingBag className="w-4 h-4 text-[#CBAC70]" />
            <span>Artikel Produk dalam Koleksi Ini ({catalogProducts.length})</span>
          </h2>

          <div className="flex items-center gap-2">
            <select
              value={sortBy}
              onChange={(e) => setSortBy(e.target.value)}
              className="bg-[#0E1736] border border-white/10 rounded-xl px-3 py-1.5 text-xs text-slate-300 focus:outline-none focus:border-[#CBAC70] cursor-pointer"
            >
              <option value="featured">Pilihan Editor</option>
              <option value="price-low">Harga Terendah</option>
              <option value="price-high">Harga Tertinggi</option>
              <option value="sold">Terlaris</option>
              <option value="rating">Rating Tertinggi</option>
            </select>
          </div>
        </div>

        {catalogProducts.length > 0 ? (
          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-5">
            {catalogProducts.map(product => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        ) : (
          <div className="py-16 text-center text-slate-400 text-xs">
            Belum ada produk spesifik pada filter ini.
          </div>
        )}
      </div>

    </div>
  );
}
