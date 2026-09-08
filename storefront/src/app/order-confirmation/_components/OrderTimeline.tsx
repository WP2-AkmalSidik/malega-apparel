import React from 'react';
import { Clock } from 'lucide-react';

interface OrderTimelineProps {
  order: any;
}

export default function OrderTimeline({ order }: OrderTimelineProps) {
  return (
    <div className="luxury-card rounded-3xl p-8 space-y-6">
      <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3 flex items-center gap-2">
        <Clock className="w-4 h-4" /> Live Tracking Timeline
      </h3>

      <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
        <div className="p-4 rounded-xl bg-[#080E20] border border-emerald-500/30 space-y-1">
          <span className="text-emerald-400 font-bold flex items-center gap-1">
            ✓ 1. Pesanan Diterima
          </span>
          <p className="text-[11px] text-[#94A3B8]">{order.createdAt}</p>
        </div>

        <div className="p-4 rounded-xl bg-[#172654] border border-[#CBAC70] shadow space-y-1">
          <span className="text-[#CBAC70] font-black flex items-center gap-1 animate-pulse">
            ● 2. Sedang Dikemas
          </span>
          <p className="text-[11px] text-[#94A3B8]">Atelier Malega Bandung</p>
        </div>

        <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 opacity-60 space-y-1">
          <span className="text-[#94A3B8] font-bold">3. Diserahkan ke Kurir</span>
          <p className="text-[11px] text-[#94A3B8]">{order.shipping?.name || 'Kurir'}</p>
        </div>

        <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 opacity-60 space-y-1">
          <span className="text-[#94A3B8] font-bold">4. Tiba di Alamat</span>
          <p className="text-[11px] text-[#94A3B8]">Estimasi {order.shipping?.etd || '1 - 2 Hari Kerja'}</p>
        </div>
      </div>
    </div>
  );
}
