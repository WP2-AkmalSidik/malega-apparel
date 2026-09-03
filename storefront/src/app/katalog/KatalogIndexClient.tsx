'use client';

import React, { useState, useMemo } from 'react';
import Link from 'next/link';
import { Layers, ArrowRight, Search, X } from 'lucide-react';
import { CatalogCollection } from '../../types';
import KatalogCard from '../../components/KatalogCard';

interface KatalogIndexClientProps {
  initialCollections: CatalogCollection[];
}

export default function KatalogIndexClient({ initialCollections }: KatalogIndexClientProps) {
  const [selectedFilter, setSelectedFilter] = useState<string>('all');
  const [searchQuery, setSearchQuery] = useState<string>('');

  const filterTabs = useMemo(() => {
    const seasons = Array.from(new Set(initialCollections.map(c => c.season).filter(Boolean)));
    return [
      { id: 'all', label: 'Semua Katalog' },
      ...seasons.map(s => ({ id: s, label: s }))
    ];
  }, [initialCollections]);

  const filteredKatalogs = useMemo(() => {
    return initialCollections.filter(c => {
      // Filter by tab
      if (selectedFilter !== 'all' && c.season !== selectedFilter) {
        return false;
      }

      // Filter by search query
      if (searchQuery.trim() !== '') {
        const q = searchQuery.toLowerCase();
        const matchTitle = (c.title || c.name || '').toLowerCase().includes(q);
        const matchSub = (c.subtitle || '').toLowerCase().includes(q);
        const matchDesc = (c.description || '').toLowerCase().includes(q);
        const matchMat = (c.featuredMaterial || '').toLowerCase().includes(q);
        const matchBadge = (c.badge || '').toLowerCase().includes(q);
        if (!matchTitle && !matchSub && !matchDesc && !matchMat && !matchBadge) return false;
      }
      return true;
    });
  }, [initialCollections, selectedFilter, searchQuery]);

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-6 space-y-6">
      
      {/* 1. Header Banner */}
      <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-4 sm:p-6 border border-[#CBAC70]/30 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <Layers className="w-4 h-4 text-[#CBAC70]" />
            <span className="text-[11px] font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
              DIREKTORI RESMI KATALOG MALEGA
            </span>
          </div>
          <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] uppercase tracking-wide">
            Katalog & Lookbook Koleksi
          </h1>
          <p className="text-xs text-[#94A3B8] max-w-lg leading-relaxed">
            Eksplorasi seri dan rilisan busana streetwear Malega. Pilih salah satu kartu katalog untuk melihat rincian lookbook serta daftar produk di dalamnya.
          </p>
        </div>

        {/* Back to Home Product List */}
        <Link
          href="/"
          className="self-start md:self-auto px-4 py-2.5 rounded-xl bg-[#070D1F] border border-[#CBAC70]/30 hover:border-[#CBAC70] text-[#CBAC70] text-xs font-bold transition-all shadow active:scale-95 flex items-center gap-1.5 shrink-0 cursor-pointer"
        >
          <span>Buka Semua Produk (Home)</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>

      {/* 2. Search & Filter Bar for Katalogs */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        
        {/* Category Tabs (Scrollable on Mobile) */}
        <div className="overflow-x-auto scrollbar-none -mx-3 px-3 sm:mx-0 sm:px-0">
          <div className="flex items-center gap-1.5 min-w-max pb-0.5">
            {filterTabs.map((tab) => (
              <button
                key={tab.id}
                onClick={() => setSelectedFilter(tab.id)}
                className={`px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all duration-200 active:scale-95 cursor-pointer ${
                  selectedFilter === tab.id
                    ? 'bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] shadow-sm'
                    : 'bg-[#0E1736] hover:bg-[#14204A] text-[#94A3B8] hover:text-[#FDFCFF] border border-white/5'
                }`}
              >
                {tab.label}
              </button>
            ))}
          </div>
        </div>

        {/* Quick Search */}
        <div className="relative w-full sm:w-64">
          <input
            type="text"
            value={searchQuery}
            onChange={(e) => setSearchQuery(e.target.value)}
            placeholder="Cari nama katalog seri..."
            className="w-full bg-[#0E1736] border border-white/10 hover:border-white/20 focus:border-[#CBAC70] rounded-xl pl-8 pr-7 py-1.5 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none transition-colors shadow-sm"
          />
          <Search className="w-3.5 h-3.5 text-[#CBAC70] absolute left-2.5 top-2.5" />
          {searchQuery && (
            <button
              onClick={() => setSearchQuery('')}
              className="absolute right-2.5 top-2 text-xs text-[#94A3B8] hover:text-white cursor-pointer"
            >
              <X className="w-3.5 h-3.5" />
            </button>
          )}
        </div>

      </div>

      {/* 3. Catalog Collections Grid */}
      <section className="space-y-3">
        <div className="flex items-center justify-between text-xs text-[#94A3B8] px-0.5">
          <span>Menampilkan <strong className="text-[#CBAC70] font-bold">{filteredKatalogs.length}</strong> seri katalog rilisan</span>
          <span className="text-[10px] font-mono text-[#94A3B8]">Malega Bespoke Lookbook</span>
        </div>

        {filteredKatalogs.length === 0 ? (
          <div className="rounded-2xl bg-[#0E1736] border border-white/10 p-10 text-center space-y-3">
            <p className="text-sm font-bold text-[#FDFCFF]">Katalog Tidak Ditemukan</p>
            <p className="text-xs text-[#94A3B8]">Tidak ada seri katalog yang sesuai dengan kata kunci atau filter yang Anda pilih.</p>
            <button
              onClick={() => { setSelectedFilter('all'); setSearchQuery(''); }}
              className="px-4 py-2 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase cursor-pointer"
            >
              Reset Filter
            </button>
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            {filteredKatalogs.map((katalog) => (
              <KatalogCard key={katalog.id || katalog.slug} collection={katalog} />
            ))}
          </div>
        )}
      </section>

    </div>
  );
}
