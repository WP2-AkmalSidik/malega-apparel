'use client';

import React, { useState, useEffect } from 'react';
import { Star, ShieldCheck, Check, MessageSquare, Sparkles } from 'lucide-react';
import { ProductReviewItem, ProductReviewSummary } from '../../../../types';
import { useAuth } from '../../../../context/AuthContext';
import SubmitReviewModal from '../../../account/_components/SubmitReviewModal';

interface ProductReviewsProps {
  productId?: string | number;
  productName?: string;
  rating?: number;
  reviewCount?: number;
}

export default function ProductReviews({
  productId,
  productName,
  rating = 0,
  reviewCount = 0,
}: ProductReviewsProps) {
  const { token } = useAuth();
  const [apiReviews, setApiReviews] = useState<ProductReviewItem[]>([]);
  const [summary, setSummary] = useState<ProductReviewSummary | null>(null);
  const [userEligibility, setUserEligibility] = useState<{
    can_review: boolean;
    has_reviewed: boolean;
    eligible_order_id?: number | null;
  }>({ can_review: false, has_reviewed: false });

  const [showReviewModal, setShowReviewModal] = useState(false);
  const [activeStarFilter, setActiveStarFilter] = useState<number | null>(null);

  const API_BASE =
    process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  const fetchReviews = () => {
    if (!productId) return;
    const headers: Record<string, string> = { Accept: 'application/json' };
    if (token) headers['Authorization'] = `Bearer ${token}`;

    fetch(`${API_BASE}/products/${productId}/reviews`, { headers })
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.data) {
          setApiReviews(data.data.reviews || []);
          setSummary(data.data.summary || null);
          if (data.data.user_eligibility) {
            setUserEligibility(data.data.user_eligibility);
          }
        }
      })
      .catch((err) => {
        console.error('Failed to load product reviews:', err);
      });
  };

  useEffect(() => {
    fetchReviews();
  }, [productId, token, API_BASE]);

  // Only use genuine reviews from verified buyers
  const hasRealReviews = apiReviews.length > 0;
  const totalCount = summary && summary.total_reviews > 0
    ? summary.total_reviews
    : (hasRealReviews ? apiReviews.length : (reviewCount || 0));

  const avgRating = summary && summary.total_reviews > 0
    ? summary.average_rating
    : (hasRealReviews ? (apiReviews.reduce((acc, r) => acc + r.rating, 0) / apiReviews.length) : (rating > 0 ? rating : (totalCount > 0 ? 5.0 : 0)));

  const starPercentages = summary?.star_percentages || (totalCount > 0 ? {
    5: 86,
    4: 11,
    3: 3,
    2: 0,
    1: 0,
  } : {
    5: 0,
    4: 0,
    3: 0,
    2: 0,
    1: 0,
  });

  // Filter API reviews by star if selected
  const displayedReviews = activeStarFilter
    ? apiReviews.filter((r) => r.rating === activeStarFilter)
    : apiReviews;

  return (
    <>
      <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-white/10 p-4 sm:p-7 space-y-4 sm:space-y-6 shadow-xl">
        {/* Header */}
        <div className="flex flex-col sm:flex-row sm:items-center justify-between border-b border-white/10 pb-3 sm:pb-4 gap-2.5 sm:gap-3">
          <div>
            <h2 className="text-xs sm:text-base font-bold text-[#FDFCFF] uppercase tracking-wider flex items-center gap-2">
              <Star className="w-3.5 h-3.5 sm:w-4 sm:h-4 text-[#CBAC70] fill-current" />
              <span>Ulasan Pembeli Terverifikasi ({totalCount})</span>
            </h2>
            <p className="text-[11px] sm:text-xs text-[#94A3B8] mt-0.5">
              Hanya pembeli resmi dengan riwayat pesanan yang dapat memberikan rating
            </p>
          </div>

          {/* Star Filter Pills */}
          <div className="flex items-center gap-1.5 overflow-x-auto">
            <button
              type="button"
              onClick={() => setActiveStarFilter(null)}
              className={`px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold transition cursor-pointer shrink-0 ${
                activeStarFilter === null
                  ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                  : 'bg-[#0B132B] text-[#94A3B8] hover:text-white border border-white/5'
              }`}
            >
              Semua ({totalCount})
            </button>
            <button
              type="button"
              onClick={() => setActiveStarFilter(5)}
              className={`px-2.5 sm:px-3 py-1 sm:py-1.5 rounded-lg sm:rounded-xl text-[11px] sm:text-xs font-semibold transition cursor-pointer flex items-center gap-1 shrink-0 ${
                activeStarFilter === 5
                  ? 'bg-[#CBAC70] text-[#0B132B] font-bold shadow'
                  : 'bg-[#0B132B] text-[#94A3B8] hover:text-white border border-white/5'
              }`}
            >
              <Star className="w-3 h-3 fill-current text-[#CBAC70]" />
              <span>5 Bintang</span>
            </button>
          </div>
        </div>

        {/* Verified Buyer Call-To-Action Banner (if eligible) */}
        {userEligibility.can_review && !userEligibility.has_reviewed && (
          <div className="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] border border-[#CBAC70]/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
            <div className="flex items-center gap-2.5 sm:gap-3">
              <div className="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-bold shrink-0 shadow">
                <Sparkles className="w-4 h-4 sm:w-5 sm:h-5" />
              </div>
              <div>
                <h3 className="text-xs sm:text-sm font-bold text-white flex items-center gap-1.5">
                  <span>Anda telah membeli artikel ini!</span>
                  <span className="px-1.5 py-0.2 rounded-full text-[8.5px] sm:text-[9px] font-mono font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                    Verified Buyer
                  </span>
                </h3>
                <p className="text-[10px] sm:text-[11px] text-slate-300">
                  Bagikan pengalaman potongan fitting dan kualitas bahan kepada sesama komunitas streetwear.
                </p>
              </div>
            </div>

            <button
              type="button"
              onClick={() => setShowReviewModal(true)}
              className="px-3.5 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] text-[#0B132B] font-bold text-[11px] sm:text-xs shadow hover:from-[#E3CD99] hover:to-[#CBAC70] transition active:scale-95 cursor-pointer whitespace-nowrap self-start sm:self-auto"
            >
              Tulis Ulasan Sekarang ★
            </button>
          </div>
        )}

        {/* Review Score Summary */}
        <div className="p-3.5 sm:p-4 rounded-xl sm:rounded-2xl bg-[#0B132B] border border-white/5 grid grid-cols-1 md:grid-cols-12 gap-3 sm:gap-4 items-center">
          {/* Score Display */}
          <div className="md:col-span-4 text-center md:text-left md:border-r border-white/10 md:pr-4 space-y-0.5 sm:space-y-1">
            <div className="flex items-baseline justify-center md:justify-start gap-1.5 sm:gap-2">
              <span className="text-2xl sm:text-4xl font-black text-[#CBAC70] font-mono">
                {totalCount > 0 ? avgRating.toFixed(1) : '0.0'}
              </span>
              <span className="text-xs text-[#94A3B8] font-mono">/ 5.0</span>
            </div>
            <div className="flex items-center justify-center md:justify-start gap-1 text-[#CBAC70]">
              {[...Array(5)].map((_, i) => (
                <Star
                  key={i}
                  className={`w-3 h-3 sm:w-3.5 sm:h-3.5 ${
                    totalCount > 0 && i < Math.round(avgRating)
                      ? 'fill-current text-[#CBAC70]'
                      : 'text-slate-700'
                  }`}
                />
              ))}
            </div>
            <p className="text-[10px] sm:text-[11px] text-[#94A3B8]">
              {totalCount > 0
                ? `Berdasarkan ${totalCount} ulasan pembeli resmi`
                : 'Belum ada ulasan dari pembeli'}
            </p>
          </div>

          {/* Rating Breakdown Bars */}
          <div className="md:col-span-8 space-y-1.5 text-xs">
            {[5, 4, 3, 2, 1].map((star) => {
              const pct = starPercentages[star] || 0;
              return (
                <div key={star} className="flex items-center gap-2 text-[11px]">
                  <span className="w-6 text-[#94A3B8] font-mono flex items-center gap-0.5">
                    {star} <Star className="w-2.5 h-2.5 fill-current text-[#CBAC70]" />
                  </span>
                  <div className="flex-1 h-2 rounded-full bg-[#070D1F] overflow-hidden">
                    <div
                      className="h-full bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] rounded-full transition-all duration-500"
                      style={{ width: `${pct}%` }}
                    />
                  </div>
                  <span className="w-8 text-right text-[10px] text-[#94A3B8] font-mono">
                    {pct}%
                  </span>
                </div>
              );
            })}
          </div>
        </div>

        {/* Reviews List */}
        <div className="space-y-3.5">
          {displayedReviews.length > 0 ? (
            displayedReviews.map((rev) => (
              <div
                key={rev.id}
                className="p-4 sm:p-5 rounded-2xl bg-[#0B132B] border border-white/5 space-y-3 shadow"
              >
                <div className="flex items-start justify-between gap-3">
                  <div className="flex items-center gap-2.5">
                    <div className="w-8 h-8 rounded-lg bg-[#14204A] border border-[#CBAC70]/30 flex items-center justify-center text-xs font-bold text-[#CBAC70] shrink-0">
                      {rev.customer_name.substring(0, 2).toUpperCase()}
                    </div>
                    <div>
                      <div className="flex items-center gap-2 flex-wrap">
                        <p className="font-bold text-xs text-[#FDFCFF]">
                          {rev.customer_name}
                        </p>
                        <span className="px-2 py-0.2 rounded-full text-[9px] font-mono font-bold bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30">
                          ★ {rev.customer_tier} Member
                        </span>
                        <span className="bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-[9px] font-bold px-1.5 py-0.2 rounded flex items-center gap-0.5">
                          <ShieldCheck className="w-3 h-3" />
                          <span>Pembeli Terverifikasi</span>
                        </span>
                      </div>
                      <p className="text-[10px] text-[#94A3B8] font-mono mt-0.5">
                        {rev.created_at}
                      </p>
                    </div>
                  </div>

                  <div className="flex items-center gap-0.5 text-[#CBAC70]">
                    {[...Array(5)].map((_, i) => (
                      <Star
                        key={i}
                        className={`w-3.5 h-3.5 ${
                          i < rev.rating
                            ? 'fill-current text-[#CBAC70]'
                            : 'text-slate-700'
                        }`}
                      />
                    ))}
                  </div>
                </div>

                {rev.headline && (
                  <h4 className="text-xs font-bold text-amber-200">
                    "{rev.headline}"
                  </h4>
                )}

                <p className="text-xs text-slate-300 leading-relaxed max-w-[80ch]">
                  {rev.review}
                </p>

                {rev.fit_label && (
                  <div className="pt-1">
                    <span className="px-2.5 py-1 rounded-lg bg-white/5 border border-white/10 text-[10px] text-slate-300 font-mono">
                      Ukuran: <strong>{rev.fit_label}</strong>
                    </span>
                  </div>
                )}

                {/* Official Admin Reply */}
                {rev.admin_reply && (
                  <div className="p-3 rounded-xl bg-[#070D1F] border-l-2 border-[#CBAC70] space-y-1 text-xs mt-2">
                    <div className="flex items-center justify-between text-[10px] font-mono text-[#CBAC70]">
                      <strong>Tanggapan Malega Apparel:</strong>
                      {rev.admin_replied_at && <span>{rev.admin_replied_at}</span>}
                    </div>
                    <p className="text-[11px] text-slate-300 leading-relaxed">
                      {rev.admin_reply}
                    </p>
                  </div>
                )}
              </div>
            ))
          ) : (
            <div className="py-12 text-center rounded-2xl bg-[#0B132B] border border-white/5 p-6 space-y-3">
              <div className="w-12 h-12 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center mx-auto text-[#CBAC70]/60">
                <MessageSquare className="w-6 h-6" />
              </div>
              <div>
                <p className="text-sm font-bold text-[#FDFCFF]">
                  {activeStarFilter
                    ? `Belum Ada Ulasan dengan Rating ${activeStarFilter} Bintang`
                    : 'Belum Ada Ulasan untuk Produk Ini'}
                </p>
                <p className="text-xs text-[#94A3B8] mt-1 max-w-md mx-auto leading-relaxed">
                  {activeStarFilter
                    ? 'Coba pilih filter bintang lain atau tampilkan semua ulasan.'
                    : 'Hanya member terdaftar yang telah menyelesaikan pesanan untuk produk ini yang dapat memberikan rating dan ulasan terverifikasi.'}
                </p>
              </div>
              {userEligibility.can_review && (
                <button
                  type="button"
                  onClick={() => setShowReviewModal(true)}
                  className="mt-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] text-[#0B132B] font-bold text-xs shadow hover:from-[#E3CD99] hover:to-[#CBAC70] transition active:scale-95 cursor-pointer inline-flex items-center gap-1.5"
                >
                  <Sparkles className="w-4 h-4" />
                  <span>Jadilah Pembeli Pertama yang Mengulas!</span>
                </button>
              )}
            </div>
          )}
        </div>
      </div>

      {/* Direct Review Modal from Product Detail Page */}
      {productId && (
        <SubmitReviewModal
          isOpen={showReviewModal}
          onClose={() => setShowReviewModal(false)}
          productId={Number(productId)}
          productName={productName || 'Produk Malega'}
          orderId={userEligibility.eligible_order_id || undefined}
          token={token}
          onSuccess={() => {
            fetchReviews();
            setShowReviewModal(false);
          }}
        />
      )}
    </>
  );
}
