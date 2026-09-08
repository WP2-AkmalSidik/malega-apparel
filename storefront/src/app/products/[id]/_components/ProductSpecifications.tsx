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
    <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2.5 sm:p-3 border border-[#CBAC70]/30 shadow-2xl">
      <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-6 space-y-5">
        <div>
          <h2 className="text-xs font-bold uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-2.5 flex items-center gap-2">
            <Sparkles className="w-4 h-4" /> Fabric & Construction Specifications
          </h2>

          <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-3.5 text-xs">
            {Object.entries(specifications || {}).map(([key, val], idx) => (
              <div
                key={idx}
                className="flex justify-between p-2.5 sm:p-3 rounded-xl bg-[#0B132B] border border-white/5"
              >
                <span className="text-[#94A3B8]">{key}</span>
                <span className="font-bold text-[#FDFCFF]">{val}</span>
              </div>
            ))}
          </div>
        </div>

        <div className="space-y-1.5 border-t border-white/10 pt-4">
          <h3 className="font-bold text-xs sm:text-sm text-[#FDFCFF] uppercase tracking-wider">
            Deskripsi & Perawatan
          </h3>
          <p className="text-xs text-[#94A3B8] leading-relaxed whitespace-pre-line">
            {description}
          </p>
        </div>
      </div>
    </div>
  );
}
