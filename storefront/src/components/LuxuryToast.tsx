import React from 'react';
import { Check } from 'lucide-react';

export interface LuxuryToastData {
  title: string;
  subtitle?: string;
}

interface LuxuryToastProps {
  toast: LuxuryToastData | null;
}

export default function LuxuryToast({ toast }: LuxuryToastProps) {
  if (!toast) return null;

  return (
    <div className="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4 pointer-events-none animate-in fade-in slide-in-from-bottom-5 duration-200">
      <div className="pointer-events-auto max-w-sm w-full bg-[#0B132B]/95 backdrop-blur-xl border border-[#CBAC70]/50 rounded-2xl p-3.5 shadow-2xl shadow-black/80 flex items-center gap-3">
        <div className="w-8 h-8 rounded-xl bg-[#CBAC70]/20 border border-[#CBAC70]/40 text-[#CBAC70] flex items-center justify-center shrink-0 shadow-sm">
          <Check className="w-4 h-4 stroke-[2.5]" />
        </div>
        <div className="min-w-0 flex-1 space-y-0.5">
          <p className="text-xs font-bold text-white tracking-tight">{toast.title}</p>
          {toast.subtitle && (
            <p className="text-[11px] font-mono text-[#CBAC70] truncate">{toast.subtitle}</p>
          )}
        </div>
      </div>
    </div>
  );
}
