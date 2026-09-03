'use client';

import React, { useState, useEffect, useRef } from 'react';
import Link from 'next/link';
import { Search, X, ArrowRight, Sparkles, Tag, Layers, Check } from 'lucide-react';
import { productsCatalog } from '../data/products';
import { Product } from '../types';

interface SearchModalProps {
  isOpen: boolean;
  onClose: () => void;
}

export default function SearchModal({ isOpen, onClose }: SearchModalProps) {
  const [query, setQuery] = useState('');
  const [selectedCategory, setSelectedCategory] = useState<string>('all');
  const inputRef = useRef<HTMLInputElement>(null);

  // Focus input on open
  useEffect(() => {
    if (isOpen) {
      setTimeout(() => {
        inputRef.current?.focus();
      }, 100);
    } else {
      setQuery('');
      setSelectedCategory('all');
    }
  }, [isOpen]);

  // Keyboard shortcut Ctrl+K or Cmd+K
  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
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

  // Filtered products
  const filteredProducts = productsCatalog.filter(product => {
    // Category filter
    if (selectedCategory !== 'all' && product.category !== selectedCategory) {
      return false;
    }

    // Query filter
    if (!query.trim()) return true;
    const q = query.toLowerCase();
    return (
      product.title.toLowerCase().includes(q) ||
      product.subtitle.toLowerCase().includes(q) ||
      product.material.toLowerCase().includes(q) ||
      product.category.toLowerCase().includes(q) ||
      `${product.gsm}gsm`.includes(q) ||
      product.description.toLowerCase().includes(q)
    );
  });

  const trendingTags = [
    '300GSM',
    'French Terry',
    'Tactical Cargo',
    'Gold Monogram Cap',
    'Raw Denim',
    'Drop Shoulder'
  ];

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 px-4 bg-black/80 backdrop-blur-md transition-opacity">
      {/* Backdrop click */}
      <div className="fixed inset-0" onClick={onClose}></div>

      {/* Search Dialog Box */}
      <div className="relative w-full max-w-2xl bg-[#0E1736] border border-[#CBAC70]/40 rounded-3xl shadow-2xl overflow-hidden z-10 animate-in fade-in zoom-in-95 duration-200">
        
        {/* Search Input Bar */}
        <div className="p-4 sm:p-5 border-b border-white/10 bg-[#070D1F]/90 flex items-center gap-3">
          <Search className="w-5 h-5 text-[#CBAC70] shrink-0" />
          <input
            ref={inputRef}
            type="text"
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Cari kaos 300GSM, hoodie, kargo, atau aksesori..."
            className="w-full bg-transparent text-sm sm:text-base text-white placeholder-slate-400 focus:outline-none"
          />
          {query && (
            <button
              onClick={() => setQuery('')}
              className="p-1 text-slate-400 hover:text-white rounded-lg hover:bg-white/10 transition"
            >
              <X className="w-4 h-4" />
            </button>
          )}
          <button
            onClick={onClose}
            className="px-2.5 py-1 text-xs font-mono font-semibold text-slate-400 hover:text-white bg-white/5 hover:bg-white/10 rounded-lg border border-white/10 transition"
          >
            ESC
          </button>
        </div>

        {/* Category Filter Pills & Trending Tags */}
        <div className="px-4 py-3 bg-[#0B132B]/80 border-b border-white/5 flex items-center justify-between gap-2 overflow-x-auto scrollbar-none">
          <div className="flex items-center gap-1.5 shrink-0">
            {['all', 'T-Shirts', 'Outerwear', 'Bottoms', 'Accessories'].map(cat => (
              <button
                key={cat}
                onClick={() => setSelectedCategory(cat)}
                className={`px-3 py-1 rounded-xl text-xs font-medium transition active:scale-95 ${
                  selectedCategory === cat
                    ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                    : 'bg-white/5 hover:bg-white/10 text-slate-300'
                }`}
              >
                {cat === 'all' ? 'Semua' : cat}
              </button>
            ))}
          </div>
        </div>

        {/* Quick Trending Suggestions */}
        {!query && (
          <div className="p-4 border-b border-white/5 bg-[#070D1F]/50">
            <p className="text-[11px] font-mono uppercase tracking-wider text-[#CBAC70] mb-2 flex items-center gap-1.5">
              <Sparkles className="w-3.5 h-3.5" />
              <span>Pencarian Populer Musim SS26</span>
            </p>
            <div className="flex items-center gap-1.5 flex-wrap">
              {trendingTags.map(tag => (
                <button
                  key={tag}
                  onClick={() => setQuery(tag)}
                  className="px-2.5 py-1 rounded-lg text-xs bg-[#14204A]/60 hover:bg-[#CBAC70]/20 text-slate-300 hover:text-[#CBAC70] border border-white/5 hover:border-[#CBAC70]/40 transition"
                >
                  #{tag}
                </button>
              ))}
            </div>
          </div>
        )}

        {/* Search Results List */}
        <div className="max-h-[60vh] overflow-y-auto divide-y divide-white/5 p-2">
          {filteredProducts.length > 0 ? (
            filteredProducts.map(product => (
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
                    className="w-14 h-14 rounded-xl object-cover border border-white/10 bg-[#070D1F] shrink-0 group-hover:border-[#CBAC70]/50 transition"
                  />
                  <div className="min-w-0">
                    <div className="flex items-center gap-2">
                      <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-white/5 text-slate-300">
                        {product.category}
                      </span>
                      {product.gsm && (
                        <span className="text-[10px] font-mono px-1.5 py-0.2 rounded bg-[#CBAC70]/15 text-[#CBAC70]">
                          {product.gsm}GSM
                        </span>
                      )}
                    </div>
                    <p className="font-bold text-sm text-slate-100 group-hover:text-[#CBAC70] transition truncate mt-0.5">
                      {product.title}
                    </p>
                    <p className="text-xs text-slate-400 truncate max-w-sm">
                      {product.subtitle}
                    </p>
                  </div>
                </div>

                <div className="text-right shrink-0">
                  <p className="font-bold font-mono text-sm text-[#CBAC70]">
                    Rp {product.price.toLocaleString('id-ID')}
                  </p>
                  {product.originalPrice > product.price && (
                    <p className="text-[11px] font-mono text-slate-500 line-through">
                      Rp {product.originalPrice.toLocaleString('id-ID')}
                    </p>
                  )}
                  <span className="text-[10px] text-emerald-400 font-mono">
                    ✓ Ready Stock
                  </span>
                </div>
              </Link>
            ))
          ) : (
            <div className="py-12 text-center text-slate-400 space-y-2">
              <p className="text-sm font-semibold text-slate-300">Tidak ada produk yang cocok dengan "{query}"</p>
              <p className="text-xs text-slate-500">Coba kata kunci lain seperti 300GSM, Hoodie, atau Monogram Cap.</p>
            </div>
          )}
        </div>

        {/* Footer info */}
        <div className="p-3 bg-[#070D1F] border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400 font-mono">
          <span>Menampilkan {filteredProducts.length} hasil produk</span>
          <Link
            href="/products"
            onClick={onClose}
            className="text-[#CBAC70] hover:underline flex items-center gap-1 font-semibold"
          >
            <span>Buka Semua Katalog</span>
            <ArrowRight className="w-3 h-3" />
          </Link>
        </div>

      </div>
    </div>
  );
}
