'use client';

import React, { useState } from 'react';
import { X, Star, ShieldCheck, CheckCircle2, AlertCircle, Loader2 } from 'lucide-react';

interface SubmitReviewModalProps {
  isOpen: boolean;
  onClose: () => void;
  productId: number;
  productName: string;
  orderId?: number;
  token: string | null;
  onSuccess: () => void;
}

export default function SubmitReviewModal({
  isOpen,
  onClose,
  productId,
  productName,
  orderId,
  token,
  onSuccess,
}: SubmitReviewModalProps) {
  const [rating, setRating] = useState<number>(5);
  const [hoverRating, setHoverRating] = useState<number>(0);
  const [headline, setHeadline] = useState('');
  const [review, setReview] = useState('');
  const [fitRating, setFitRating] = useState<'true_to_size' | 'runs_small' | 'runs_large'>('true_to_size');
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [errorMessage, setErrorMessage] = useState<string | null>(null);
  const [isSuccess, setIsSuccess] = useState(false);

  if (!isOpen) return null;

  const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  const getRatingLabel = (r: number) => {
    switch (r) {
      case 5:
        return 'Sangat Puas! (Kualitas & Cutting Sempurna)';
      case 4:
        return 'Puas (Bagus & Sesuai Ekspektasi)';
      case 3:
        return 'Cukup (Kualitas Standar)';
      case 2:
        return 'Kurang Puas';
      case 1:
        return 'Sangat Kecewa';
      default:
        return '';
    }
  };

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!token) {
      setErrorMessage('Silakan masuk ke akun Anda terlebih dahulu.');
      return;
    }

    if (review.trim().length < 5) {
      setErrorMessage('Ulasan minimal berisi 5 karakter.');
      return;
    }

    setIsSubmitting(true);
    setErrorMessage(null);

    try {
      const res = await fetch(`${API_BASE}/products/${productId}/reviews`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          Authorization: `Bearer ${token}`,
        },
        body: JSON.stringify({
          rating,
          headline: headline.trim() || undefined,
          review: review.trim(),
          fit_rating: fitRating,
          order_id: orderId,
        }),
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        setErrorMessage(data.message || 'Gagal mengirim ulasan.');
        setIsSubmitting(false);
        return;
      }

      setIsSuccess(true);
      onSuccess();
      setTimeout(() => {
        setIsSuccess(false);
        onClose();
      }, 1500);
    } catch (err) {
      setErrorMessage('Terjadi kendala jaringan. Silakan coba kembali.');
      setIsSubmitting(false);
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 overflow-y-auto animate-in fade-in duration-200">
      <div className="bg-[#0B132B] border border-[#CBAC70]/40 rounded-3xl max-w-lg w-full my-auto shadow-2xl overflow-hidden flex flex-col text-[#FDFCFF] max-h-[92vh]">
        {/* Modal Header */}
        <div className="p-5 border-b border-white/10 flex items-center justify-between bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024]">
          <div>
            <div className="flex items-center gap-1.5 text-[10px] font-mono text-emerald-400 font-bold uppercase">
              <ShieldCheck className="w-3.5 h-3.5" />
              <span>Verified Buyer Review</span>
            </div>
            <h2 className="text-sm sm:text-base font-bold text-white truncate max-w-xs sm:max-w-md mt-0.5">
              Beri Ulasan: {productName}
            </h2>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="p-1.5 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition cursor-pointer"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Modal Body */}
        <div className="p-5 space-y-4 overflow-y-auto">
          {isSuccess ? (
            <div className="py-8 text-center space-y-2">
              <CheckCircle2 className="w-12 h-12 text-emerald-400 mx-auto animate-bounce" />
              <h3 className="text-base font-bold text-white">Ulasan Berhasil Terkirim!</h3>
              <p className="text-xs text-slate-400">
                Terima kasih atas ulasan Anda. Penilaian Anda sangat berharga bagi komunitas Malega Apparel.
              </p>
            </div>
          ) : (
            <form onSubmit={handleSubmit} className="space-y-4 text-xs">
              {errorMessage && (
                <div className="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs flex items-center gap-2">
                  <AlertCircle className="w-4 h-4 shrink-0 text-rose-400" />
                  <span>{errorMessage}</span>
                </div>
              )}

              {/* Star Rating Picker */}
              <div className="space-y-1.5 text-center p-3 rounded-2xl bg-black/40 border border-white/5">
                <label className="block text-[11px] font-mono uppercase tracking-wider text-slate-300 font-semibold">
                  Berapa rating untuk artikel ini?
                </label>
                <div className="flex items-center justify-center gap-2 py-1">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <button
                      key={star}
                      type="button"
                      onClick={() => setRating(star)}
                      onMouseEnter={() => setHoverRating(star)}
                      onMouseLeave={() => setHoverRating(0)}
                      className="p-1 hover:scale-125 transition-transform cursor-pointer"
                    >
                      <Star
                        className={`w-7 h-7 sm:w-8 sm:h-8 transition-colors ${
                          star <= (hoverRating || rating)
                            ? 'text-[#CBAC70] fill-current drop-shadow-[0_0_8px_rgba(203,172,112,0.5)]'
                            : 'text-slate-700'
                        }`}
                      />
                    </button>
                  ))}
                </div>
                <p className="text-[11px] font-mono text-[#CBAC70] font-bold">
                  {getRatingLabel(hoverRating || rating)}
                </p>
              </div>

              {/* Fit Rating Selector */}
              <div className="space-y-1.5">
                <label className="block text-[11px] font-mono uppercase tracking-wider text-slate-300 font-semibold">
                  Bagaimana kecocokan ukuran (Fit)?
                </label>
                <div className="grid grid-cols-3 gap-2">
                  {[
                    { id: 'runs_small', label: 'Agak Kecil' },
                    { id: 'true_to_size', label: 'Pas di Badan' },
                    { id: 'runs_large', label: 'Agak Besar' },
                  ].map((item) => (
                    <button
                      key={item.id}
                      type="button"
                      onClick={() => setFitRating(item.id as any)}
                      className={`py-2 px-2 text-center rounded-xl font-bold transition text-xs cursor-pointer border ${
                        fitRating === item.id
                          ? 'bg-[#CBAC70] text-[#0B132B] border-[#CBAC70] shadow-md'
                          : 'bg-[#0E1736] text-slate-400 border-white/10 hover:border-white/20'
                      }`}
                    >
                      {item.label}
                    </button>
                  ))}
                </div>
              </div>

              {/* Headline / Title */}
              <div className="space-y-1">
                <label className="block text-[11px] font-mono uppercase tracking-wider text-slate-300 font-semibold">
                  Judul Ulasan (Opsional)
                </label>
                <input
                  type="text"
                  value={headline}
                  onChange={(e) => setHeadline(e.target.value)}
                  placeholder="Contoh: Bahan adem, cutting oversized pas banget!"
                  maxLength={100}
                  className="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                />
              </div>

              {/* Review Comment */}
              <div className="space-y-1">
                <label className="block text-[11px] font-mono uppercase tracking-wider text-slate-300 font-semibold">
                  Pengalaman Penggunaan <span className="text-rose-400">*</span>
                </label>
                <textarea
                  required
                  rows={4}
                  value={review}
                  onChange={(e) => setReview(e.target.value)}
                  placeholder="Bagikan pengalaman Anda mengenai kualitas jahitan, kenyamanan bahan katun, atau lookbook outfit saat dipakai..."
                  maxLength={1000}
                  className="w-full bg-[#070C1A] border border-white/10 rounded-xl px-3 py-2.5 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                />
                <div className="flex justify-between text-[10px] text-slate-500 font-mono">
                  <span>Minimal 5 karakter</span>
                  <span>{review.length}/1000</span>
                </div>
              </div>

              {/* Verified Buyer Note */}
              <div className="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-[11px] text-emerald-300 flex items-center gap-2">
                <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
                <span>
                  Ulasan Anda akan dipublikasikan dengan lencana <strong>Pembeli Terverifikasi</strong>.
                </span>
              </div>

              {/* Submit Buttons */}
              <div className="pt-2 border-t border-white/10 flex items-center justify-end gap-2">
                <button
                  type="button"
                  onClick={onClose}
                  disabled={isSubmitting}
                  className="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
                >
                  Batal
                </button>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="px-5 py-2.5 rounded-xl text-xs font-bold text-[#0B132B] bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] hover:from-[#E3CD99] hover:to-[#CBAC70] transition shadow-lg flex items-center gap-1.5 active:scale-95 disabled:opacity-50 cursor-pointer"
                >
                  {isSubmitting ? (
                    <>
                      <Loader2 className="w-4 h-4 animate-spin" />
                      <span>Mengirim...</span>
                    </>
                  ) : (
                    <span>Kirim Ulasan & Rating ★</span>
                  )}
                </button>
              </div>
            </form>
          )}
        </div>
      </div>
    </div>
  );
}
