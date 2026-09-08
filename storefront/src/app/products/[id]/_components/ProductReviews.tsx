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
    <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-[#CBAC70]/30 p-5 sm:p-8 space-y-6 shadow-2xl">
      <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-4 gap-3">
        <div>
          <h2 className="text-sm sm:text-base font-black text-[#FDFCFF] uppercase tracking-wider flex items-center gap-2">
            <Star className="w-4 h-4 text-[#CBAC70] fill-current" />
            <span>Ulasan Pembeli Terverifikasi ({reviewCount})</span>
          </h2>
          <p className="text-xs text-[#94A3B8] mt-0.5">
            Rating Kepuasan 4.9/5 dari pelanggan streetwear se-Indonesia
          </p>
        </div>

        <div className="flex items-center gap-1.5 overflow-x-auto">
          <button
            type="button"
            onClick={() => setReviewFilter('all')}
            className={`px-3 py-1.5 rounded-xl text-xs font-semibold transition cursor-pointer ${
              reviewFilter === 'all'
                ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                : 'bg-[#0B132B] text-[#94A3B8] hover:text-white'
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
                : 'bg-[#0B132B] text-[#94A3B8] hover:text-white'
            }`}
          >
            <ImageIcon className="w-3.5 h-3.5" />
            <span>Dengan Foto</span>
          </button>
        </div>
      </div>

      {/* Reviews List */}
      <div className="space-y-4">
        {filteredReviews.map((rev) => (
          <div
            key={rev.id}
            className="p-4 rounded-2xl bg-[#070D1F] border border-white/5 space-y-2.5"
          >
            <div className="flex items-center justify-between">
              <div className="flex items-center gap-2.5">
                <div className="w-7 h-7 rounded-lg bg-[#14204A] border border-[#CBAC70]/40 flex items-center justify-center text-xs font-bold text-[#CBAC70]">
                  {rev.avatar}
                </div>
                <div>
                  <p className="font-bold text-xs text-[#FDFCFF]">{rev.author}</p>
                  <p className="text-[10px] text-[#94A3B8] font-mono">{rev.variant}</p>
                </div>
              </div>

              <div className="flex items-center gap-1 text-[#CBAC70]">
                {[...Array(rev.rating)].map((_, i) => (
                  <Star key={i} className="w-3.5 h-3.5 fill-current" />
                ))}
              </div>
            </div>

            <p className="text-xs text-slate-300 leading-relaxed">{rev.comment}</p>

            {rev.photos.length > 0 && (
              <div className="flex items-center gap-2 pt-1">
                {rev.photos.map((img, pIdx) => (
                  <img
                    key={pIdx}
                    src={img}
                    alt="Fit pic"
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
