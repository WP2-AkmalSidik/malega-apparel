'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { 
  ArrowLeft, 
  ShoppingBag
} from 'lucide-react';
import { Product, CatalogCollection } from '../../../types';
import { katalogCollections } from '../../../data/katalog';
import { productsCatalog } from '../../../data/products';
import ProductCard from '../../../components/ProductCard';

interface KatalogDetailClientProps {
  slug: string;
  initialCollection?: CatalogCollection | null;
  initialProducts?: Product[];
}

export default function KatalogDetailClient({ slug, initialCollection, initialProducts }: KatalogDetailClientProps) {
  const katalog = useMemo(() => {
    if (initialCollection) return initialCollection;
    return katalogCollections.find(c => c.slug === slug || c.id === slug) || katalogCollections[0];
  }, [initialCollection, slug]);

  const [sortBy, setSortBy] = useState<string>('featured');

  // Filter products that belong to this catalog
  const catalogProducts = useMemo(() => {
    let list: Product[] = [];

    if (katalog.products && katalog.products.length > 0) {
      list = [...katalog.products];
    } else {
      const pool = (initialProducts && initialProducts.length > 0) ? initialProducts : productsCatalog;
      list = pool.filter(p => {
        const isInIdList = (katalog.productIds || []).includes(p.id);
        const isMatchingCategory = (katalog.slug.includes('tee') && p.category === 'T-Shirts') ||
          (katalog.slug.includes('outerwear') && p.category === 'Outerwear') ||
          (katalog.slug.includes('bottoms') && p.category === 'Bottoms') ||
          (katalog.slug.includes('accessories') && p.category === 'Accessories') ||
          (katalog.slug.includes('capsule') && p.isNewDrop) ||
          (katalog.slug.includes('bestseller') && p.isBestSeller);

        return isInIdList || isMatchingCategory;
      });
    }

    return list.sort((a, b) => {
      if (sortBy === 'price-low') return a.price - b.price;
      if (sortBy === 'price-high') return b.price - a.price;
      if (sortBy === 'sold') return (b.soldCount || 0) - (a.soldCount || 0);
      if (sortBy === 'rating') return (b.rating || 0) - (a.rating || 0);
      return 0;
    });
  }, [katalog, initialProducts, sortBy]);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6">
      
      {/* 1. Back to Directory Bar */}
      <div className="flex items-center justify-between text-xs">
        <Link
          href="/katalog"
          className="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-[#CBAC70] font-bold transition cursor-pointer"
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
            {katalog.badge && (
              <span className="px-2.5 py-0.5 rounded-md text-[10px] font-mono font-bold bg-[#CBAC70] text-[#0B132B]">
                {katalog.badge}
              </span>
            )}
            <h1 className="text-2xl sm:text-3xl font-black text-white uppercase tracking-wide">
              {katalog.title || katalog.name}
            </h1>
            {katalog.subtitle && (
              <p className="text-xs sm:text-sm text-[#CBAC70] font-semibold">
                {katalog.subtitle}
              </p>
            )}
          </div>

          {katalog.palette && katalog.palette.length > 0 && (
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
          )}
        </div>

        {katalog.storytelling ? (
          <p className="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-3xl">
            {katalog.storytelling}
          </p>
        ) : (
          <p className="text-xs sm:text-sm text-slate-300 leading-relaxed max-w-3xl">
            {katalog.description}
          </p>
        )}
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
          <div className="p-8 text-center bg-white/5 rounded-2xl border border-white/10 space-y-2">
            <p className="text-xs text-slate-400">Belum ada artikel produk aktif yang terhubung ke lookbook ini.</p>
            <Link
              href="/"
              className="inline-block px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] text-xs font-bold uppercase cursor-pointer"
            >
              Lihat Semua Produk di Beranda
            </Link>
          </div>
        )}
      </div>

    </div>
  );
}
