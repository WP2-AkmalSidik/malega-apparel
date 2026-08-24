'use client';

import React, { useState, useMemo, use } from 'react';
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

interface PageProps {
  params: Promise<{ slug: string }>;
}

export default function KatalogDetailPage({ params }: PageProps) {
  const resolvedParams = use(params);
  
  const katalog = katalogCollections.find(c => c.slug === resolvedParams.slug || c.id === resolvedParams.slug) || katalogCollections[0];

  const [searchQuery, setSearchQuery] = useState<string>('');
  const [sortBy, setSortBy] = useState<string>('featured');

  // Filter products that belong to this catalog
  const catalogProducts = useMemo(() => {
    return productsCatalog.filter(p => {
      // If matching by productIds list or category
      const isInIdList = katalog.productIds.includes(p.id);
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
      
      {/* 1. Header with Back Button */}
      <div className="flex items-center justify-between">
        <Link
          href="/katalog"
          className="inline-flex items-center gap-1.5 text-xs font-bold text-[#CBAC70] hover:text-white transition-colors bg-[#0E1736] border border-[#CBAC70]/30 px-3 py-1.5 rounded-xl shadow-sm active:scale-95"
        >
          <ArrowLeft className="w-3.5 h-3.5" />
          <span>Kembali ke Direktori Katalog</span>
        </Link>
        <span className="text-[11px] text-[#94A3B8] font-mono">
          {katalog.season} • {katalog.releaseYear}
        </span>
      </div>

      {/* 2. Editorial Collection Hero Banner (Double-Wrapped) */}
      <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl">
        <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-5 sm:p-8 relative overflow-hidden space-y-4">
          
          <div className="flex items-center gap-2">
            <span className="bg-[#CBAC70] text-[#0B132B] font-black text-[9px] sm:text-[10px] tracking-widest uppercase px-2.5 py-0.5 rounded shadow">
              {katalog.badge}
            </span>
            <span className="text-xs text-[#94A3B8] font-mono">| {katalog.featuredMaterial}</span>
          </div>

          <div className="space-y-1 max-w-2xl">
            <h1 className="text-2xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-wide">
              {katalog.title}
            </h1>
            <p className="text-xs sm:text-sm text-[#CBAC70] font-semibold">
              {katalog.subtitle}
            </p>
            <p className="text-xs text-[#94A3B8] leading-relaxed pt-2">
              {katalog.description}
            </p>
            <p className="text-[11px] text-[#94A3B8]/80 italic pt-1 border-t border-white/5">
              &quot;{katalog.storytelling}&quot;
            </p>
          </div>

          {/* Tags */}
          <div className="flex flex-wrap gap-1.5 pt-2">
            {katalog.tags.map((t, idx) => (
              <span key={idx} className="bg-[#14204A] text-[#CBAC70] border border-[#CBAC70]/20 text-[10px] font-mono px-2.5 py-0.5 rounded-lg">
                #{t}
              </span>
            ))}
          </div>

        </div>
      </div>

      {/* 3. Search & Sort Toolbar inside this Catalog */}
      <div className="flex items-center gap-2">
        <div className="relative flex-1">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder={`Cari artikel dalam katalog ${katalog.title}...`}
            className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-8 sm:pl-9 pr-7 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
          />
          <Search className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] absolute left-2.5 sm:left-3 top-2.5" />
          {searchQuery && (
            <button onClick={() => setSearchQuery('')} className="absolute right-2.5 top-2 text-xs text-[#94A3B8] hover:text-white">
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

        <div className="flex items-center gap-1.5 bg-[#0E1736] border border-white/10 rounded-xl px-2.5 py-1.5 shrink-0">
          <ArrowUpDown className="w-3.5 h-3.5 text-[#CBAC70]" />
          <select
            value={sortBy}
            onChange={(e) => setSortBy(e.target.value)}
            className="bg-transparent text-xs text-[#FDFCFF] focus:outline-none cursor-pointer pr-1"
          >
            <option value="featured" className="bg-[#0B132B]">Paling Populer</option>
            <option value="sold" className="bg-[#0B132B]">Terlaris (Sold)</option>
            <option value="price-low" className="bg-[#0B132B]">Harga: Rendah ke Tinggi</option>
            <option value="price-high" className="bg-[#0B132B]">Harga: Tinggi ke Rendah</option>
            <option value="rating" className="bg-[#0B132B]">Rating Tertinggi</option>
          </select>
        </div>
      </div>

      {/* 4. Products in this Catalog Series */}
      <section className="space-y-3">
        <div className="flex items-center justify-between text-[11px] text-[#94A3B8] px-0.5">
          <span>Menampilkan <strong className="text-[#CBAC70] font-bold">{catalogProducts.length}</strong> artikel produk dalam katalog ini</span>
          <span className="text-[#CBAC70] font-mono text-[10px]">100% Original Malega Studio</span>
        </div>

        {catalogProducts.length === 0 ? (
          <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-10 text-center space-y-3">
            <p className="text-sm font-bold text-[#FDFCFF]">Tidak ada produk yang cocok dengan kata kunci</p>
            <button
              onClick={() => setSearchQuery('')}
              className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase"
            >
              Reset Pencarian
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2.5 sm:gap-4 lg:gap-5">
            {catalogProducts.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}
      </section>

    </div>
  );
}
