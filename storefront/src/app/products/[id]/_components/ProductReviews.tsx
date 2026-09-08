'use client';

import React from 'react';
import { Star, Image as ImageIcon } from 'lucide-react';
import { ReviewItem } from '../_constants/reviews-data';

interface ProductReviewsProps {
  reviewCount: number;
  reviewsList: ReviewItem[];
  filteredReviews: ReviewItem[];
  reviewFilter: 'all' | 'photo' | '5star';
  setReviewFilter: (filter: 'all' | 'photo' | '5star') => void;
}

export default function ProductReviews({
  reviewCount,
  reviewsList,
  filteredReviews,
  reviewFilter,
  setReviewFilter,
}: ProductReviewsProps) {
  return (
    <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-7 space-y-6 shadow-xl">
      {/* Header & Filter Tabs */}
      <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-4 gap-3">
        <div>
          <h2 className="text-sm sm:text-base font-bold text-[#FDFCFF] uppercase tracking-wider flex items-center gap-2">
            <Star className="w-4 h-4 text-[#CBAC70] fill-current" />
            <span>Ulasan Pelanggan ({reviewCount})</span>
          </h2>
          <p className="text-xs text-[#94A3B8] mt-0.5">
            Ditinjau oleh pembeli terverifikasi dari seluruh Indonesia
          </p>
        </div>

        <div className="flex items-center gap-1.5 overflow-x-auto">
          <button
            type="button"
            onClick={() => setReviewFilter('all')}
            className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer ${
              reviewFilter === 'all'
                ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                : 'bg-[#0B132B] text-[#94A3B8] hover:text-white border border-white/5'
            }`}
          >
            Semua ({reviewsList.length})
          </button>
          <button
            type="button"
            onClick={() => setReviewFilter('photo')}
            className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer flex items-center gap-1 ${
              reviewFilter === 'photo'
                ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                : 'bg-[#0B132B] text-[#94A3B8] hover:text-white border border-white/5'
            }`}
          >
            <ImageIcon className="w-3.5 h-3.5" />
            <span>Dengan Foto</span>
          </button>
        </div>
      </div>

      {/* Review Score Summary (Section 22) */}
      <div className="p-4 rounded-xl bg-[#0B132B] border border-white/5 grid grid-cols-1 md:grid-cols-12 gap-4 items-center">
        {/* Score Display */}
        <div className="md:col-span-4 text-center md:text-left md:border-r border-white/10 md:pr-4 space-y-1">
          <div className="flex items-baseline justify-center md:justify-start gap-2">
            <span className="text-3xl sm:text-4xl font-black text-[#CBAC70]">4.9</span>
            <span className="text-xs text-[#94A3B8] font-mono">/ 5.0</span>
          </div>
          <div className="flex items-center justify-center md:justify-start gap-1 text-[#CBAC70]">
            {[...Array(5)].map((_, i) => (
              <Star key={i} className="w-3.5 h-3.5 fill-current" />
            ))}
          </div>
          <p className="text-[11px] text-[#94A3B8]">Berdasarkan {reviewCount} ulasan pembeli</p>
        </div>

        {/* Rating Breakdown Bars */}
        <div className="md:col-span-8 space-y-1.5 text-xs">
          {[
            { star: 5, pct: 88, count: Math.round(reviewCount * 0.88) },
            { star: 4, pct: 10, count: Math.round(reviewCount * 0.10) },
            { star: 3, pct: 2, count: Math.max(1, Math.round(reviewCount * 0.02)) },
          ].map((item) => (
            <div key={item.star} className="flex items-center gap-2 text-[11px]">
              <span className="w-6 text-[#94A3B8] font-mono flex items-center gap-0.5">
                {item.star} <Star className="w-2.5 h-2.5 fill-current text-[#CBAC70]" />
              </span>
              <div className="flex-1 h-2 rounded-full bg-[#070D1F] overflow-hidden">
                <div
                  className="h-full bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] rounded-full"
                  style={{ width: `${item.pct}%` }}
                />
              </div>
              <span className="w-8 text-right text-[10px] text-[#94A3B8] font-mono">{item.pct}%</span>
            </div>
          ))}
        </div>
      </div>

      {/* Reviews List */}
      <div className="space-y-3.5">
        {filteredReviews.map((rev) => (
          <div
            key={rev.id}
            className="p-4 rounded-xl bg-[#0B132B] border border-white/5 space-y-2.5"
          >
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <div className="w-8 h-8 rounded-lg bg-[#14204A] border border-[#CBAC70]/30 flex items-center justify-center text-xs font-bold text-[#CBAC70]">
                  {rev.avatar}
                </div>
                <div>
                  <div className="flex items-center gap-2">
                    <p className="font-bold text-xs text-[#FDFCFF]">{rev.author}</p>
                    <span className="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold px-1.5 py-0.2 rounded">
                      ✓ Terverifikasi
                    </span>
                  </div>
                  <p className="text-[10px] text-[#94A3B8] font-mono">{rev.variant}</p>
                </div>
              </div>

              <div className="flex items-center gap-0.5 text-[#CBAC70]">
                {[...Array(rev.rating)].map((_, i) => (
                  <Star key={i} className="w-3.5 h-3.5 fill-current" />
                ))}
              </div>
            </div>

            <p className="text-xs text-slate-300 leading-relaxed max-w-[75ch]">{rev.comment}</p>

            {rev.photos.length > 0 && (
              <div className="flex items-center gap-2 pt-1">
                {rev.photos.map((img, pIdx) => (
                  <img
                    key={pIdx}
                    src={img}
                    alt="Fit pic review"
                    className="w-16 h-16 rounded-xl object-cover border border-white/10"
                  />
                ))}
              </div>
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
