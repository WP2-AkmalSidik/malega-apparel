'use client';

import React from 'react';
import { Ruler } from 'lucide-react';

interface SizeChartModalProps {
  showSizeChart: boolean;
  setShowSizeChart: (show: boolean) => void;
  isNumericSizeProduct: boolean;
  isAllSizeProduct: boolean;
  sizes?: string[];
  selectedSize: string;
  setSelectedSize: (size: string) => void;
}

export default function SizeChartModal({
  showSizeChart,
  setShowSizeChart,
  isNumericSizeProduct,
  isAllSizeProduct,
  sizes,
  selectedSize,
  setSelectedSize,
}: SizeChartModalProps) {
  if (!showSizeChart) return null;

  return (
    <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-4">
      <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-2xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 animate-in zoom-in-95 text-[#FDFCFF]">
        <div className="flex items-center justify-between border-b border-white/10 pb-3">
          <div>
            <h3 className="font-bold text-xs sm:text-sm text-[#CBAC70] uppercase tracking-wider flex items-center gap-2">
              <Ruler className="w-4 h-4" />
              {isNumericSizeProduct
                ? 'Size Chart Guide (Pants & Denim / Celana)'
                : isAllSizeProduct
                ? 'Size Chart Guide (All Size / Adjustable)'
                : 'Size Chart Guide (Boxy Oversized & Tops)'}
            </h3>
            <p className="text-[10px] text-slate-400 mt-0.5">
              Disesuaikan khusus untuk varian ukuran produk ini ({sizes?.join(', ') || 'Standar'})
            </p>
          </div>
          <button
            type="button"
            onClick={() => setShowSizeChart(false)}
            className="text-[#94A3B8] hover:text-white cursor-pointer"
          >
            ✕
          </button>
        </div>

        <div className="overflow-x-auto text-xs">
          {isNumericSizeProduct ? (
            /* TABEL UKURAN NOMOR / ANGKA */
            <table className="w-full text-left border border-white/10 rounded-lg overflow-hidden">
              <thead className="bg-[#080E20] text-[#CBAC70] font-bold text-[11px]">
                <tr>
                  <th className="p-2.5">Size</th>
                  <th className="p-2.5">Lingkar Pinggang</th>
                  <th className="p-2.5">Panjang Celana</th>
                  <th className="p-2.5">Lingkar Paha</th>
                  <th className="p-2.5">Open Leg</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/5 text-[#94A3B8]">
                {(sizes && sizes.length > 0
                  ? sizes
                  : ['28', '30', '32', '34', '36']
                ).map((sizeItem, idx) => {
                  const sizeNum = parseInt(sizeItem) || 30;
                  const waist = Math.round(sizeNum * 2.54) + ' cm';
                  const length =
                    Math.min(100 + Math.round((sizeNum - 26) * 0.7), 110) + ' cm';
                  const thigh = 48 + Math.round((sizeNum - 26) * 1.5) + ' cm';
                  const leg = 34 + Math.round((sizeNum - 26) * 1.0) + ' cm';
                  const isCurrent = String(selectedSize) === String(sizeItem);

                  return (
                    <tr
                      key={idx}
                      onClick={() => setSelectedSize(sizeItem)}
                      className={`cursor-pointer transition-colors ${
                        isCurrent
                          ? 'bg-[#CBAC70]/20 text-[#CBAC70] font-bold'
                          : 'hover:bg-white/5'
                      }`}
                    >
                      <td className="p-2.5 font-bold text-white flex items-center gap-1.5">
                        <span>{sizeItem}</span>
                        {isCurrent && (
                          <span className="text-[9px] px-1.5 py-0.5 rounded bg-[#CBAC70] text-[#0B132B] font-bold">
                            Dipilih
                          </span>
                        )}
                      </td>
                      <td className="p-2.5">{waist}</td>
                      <td className="p-2.5">{length}</td>
                      <td className="p-2.5">{thigh}</td>
                      <td className="p-2.5">{leg}</td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          ) : isAllSizeProduct ? (
            /* INFO UKURAN ALL SIZE / AKSESORIS */
            <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 space-y-3">
              <div className="flex items-center justify-between border-b border-white/5 pb-2">
                <span className="text-slate-400">Ukuran Produk:</span>
                <span className="font-bold text-[#CBAC70]">
                  All Size / One Size Fits All
                </span>
              </div>
              <div className="text-xs text-slate-300 space-y-2 leading-relaxed">
                <p>
                  • <strong>Topi / Cap:</strong> Lingkar kepala 56 - 60 cm dengan strap
                  pengatur logam (Adjustable Brass Strap).
                </p>
                <p>
                  • <strong>Tas / Bags:</strong> Dimensi kompartemen dirancang modular
                  untuk penggunaan harian (Daily Streetwear Utility).
                </p>
                <p>
                  • <strong>Ikat Pinggang / Belt:</strong> Panjang 115 - 125 cm dengan
                  lubang standar fleksibel.
                </p>
              </div>
            </div>
          ) : (
            /* TABEL UKURAN ABJAD (S-XXL) */
            <table className="w-full text-left border border-white/10 rounded-lg overflow-hidden">
              <thead className="bg-[#080E20] text-[#CBAC70] font-bold text-[11px]">
                <tr>
                  <th className="p-2.5">Size</th>
                  <th className="p-2.5">Lebar Dada</th>
                  <th className="p-2.5">Panjang Baju</th>
                  <th className="p-2.5">Panjang Lengan</th>
                </tr>
              </thead>
              <tbody className="divide-y divide-white/5 text-[#94A3B8]">
                {(sizes && sizes.length > 0
                  ? sizes
                  : ['S', 'M', 'L', 'XL', 'XXL']
                ).map((sizeItem, idx) => {
                  const standard: Record<
                    string,
                    { chest: string; length: string; sleeve: string }
                  > = {
                    XS: { chest: '51 cm', length: '66 cm', sleeve: '23 cm' },
                    S: { chest: '54 cm', length: '68 cm', sleeve: '24 cm' },
                    M: { chest: '57 cm', length: '71 cm', sleeve: '25 cm' },
                    L: { chest: '60 cm', length: '74 cm', sleeve: '26 cm' },
                    XL: { chest: '63 cm', length: '77 cm', sleeve: '27 cm' },
                    XXL: { chest: '66 cm', length: '80 cm', sleeve: '28 cm' },
                    '2XL': { chest: '66 cm', length: '80 cm', sleeve: '28 cm' },
                    XXXL: { chest: '69 cm', length: '82 cm', sleeve: '29 cm' },
                    '3XL': { chest: '69 cm', length: '82 cm', sleeve: '29 cm' },
                  };
                  const upper = sizeItem.toUpperCase().trim();
                  const m = standard[upper] || {
                    chest: '58 cm',
                    length: '72 cm',
                    sleeve: '25 cm',
                  };
                  const isCurrent = String(selectedSize) === String(sizeItem);

                  return (
                    <tr
                      key={idx}
                      onClick={() => setSelectedSize(sizeItem)}
                      className={`cursor-pointer transition-colors ${
                        isCurrent
                          ? 'bg-[#CBAC70]/20 text-[#CBAC70] font-bold'
                          : 'hover:bg-white/5'
                      }`}
                    >
                      <td className="p-2.5 font-bold text-white flex items-center gap-1.5">
                        <span>{sizeItem}</span>
                        {isCurrent && (
                          <span className="text-[9px] px-1.5 py-0.5 rounded bg-[#CBAC70] text-[#0B132B] font-bold">
                            Dipilih
                          </span>
                        )}
                      </td>
                      <td className="p-2.5">{m.chest}</td>
                      <td className="p-2.5">{m.length}</td>
                      <td className="p-2.5">{m.sleeve}</td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          )}
        </div>

        <div className="flex items-center justify-between text-[10px] text-[#94A3B8]">
          <span className="italic">
            {isNumericSizeProduct
              ? '* Ukuran standar inci celana denim/cargo internasional. Toleransi jahit 1-2 cm.'
              : isAllSizeProduct
              ? '* Produk dirancang fleksibel menyesuaikan bentuk pemakaian.'
              : '* Potongan boxy cut kami sudah drop-shoulder oversized. Toleransi jahit 1-2 cm.'}
          </span>
          <span className="text-gold font-mono">Klik baris untuk memilih ukuran</span>
        </div>

        <button
          type="button"
          onClick={() => setShowSizeChart(false)}
          className="w-full py-2.5 bg-[#CBAC70] text-[#0B132B] font-bold text-xs uppercase tracking-wider rounded-xl hover:bg-[#E3CD99] transition cursor-pointer"
        >
          Tutup Panduan
        </button>
      </div>
    </div>
  );
}
