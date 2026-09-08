import React from 'react';
import { Sparkles } from 'lucide-react';

interface ProductSpecificationsProps {
  specifications?: Record<string, string>;
  description: string;
}

export default function ProductSpecifications({
  specifications,
  description,
}: ProductSpecificationsProps) {
  return (
    <div className="rounded-2xl sm:rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-7 space-y-6 shadow-xl">
      <div>
        <h2 className="text-xs sm:text-sm font-bold uppercase tracking-wider text-[#CBAC70] border-b border-white/10 pb-3 flex items-center gap-2">
          <Sparkles className="w-4 h-4 text-[#CBAC70]" />
          <span>Detail Produk & Material</span>
        </h2>

        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3.5 text-xs">
          {Object.entries(specifications || {}).map(([key, val], idx) => (
            <div
              key={idx}
              className="flex justify-between items-center p-3 rounded-xl bg-[#0B132B] border border-white/5"
            >
              <span className="text-[#94A3B8] font-medium">{key}</span>
              <span className="font-bold text-[#FDFCFF] text-right">{val}</span>
            </div>
          ))}
        </div>
      </div>

      <div className="space-y-2 border-t border-white/10 pt-4">
        <h3 className="font-bold text-xs sm:text-sm text-[#FDFCFF] uppercase tracking-wider">
          Deskripsi & Perawatan
        </h3>
        <p className="text-xs text-[#94A3B8] leading-relaxed whitespace-pre-line max-w-3xl">
          {description}
        </p>
      </div>
    </div>
  );
}
