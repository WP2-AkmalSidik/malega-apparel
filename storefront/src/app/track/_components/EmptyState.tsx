import React from 'react';
import { Truck } from 'lucide-react';

export default function EmptyState() {
  return (
    <div className="space-y-5 animate-fade-in py-2">
      {/* Guide Hero Card */}
      <div className="p-6 sm:p-8 rounded-3xl bg-[#0B132B]/90 border border-white/10 shadow-2xl relative overflow-hidden text-center space-y-4">
        <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-80" />

        <div className="w-14 h-14 sm:w-16 sm:h-16 mx-auto rounded-2xl bg-[#CBAC70]/10 border border-[#CBAC70]/30 flex items-center justify-center text-[#CBAC70] shadow-lg shadow-[#CBAC70]/10">
          <Truck className="w-7 h-7 sm:w-8 sm:h-8" />
        </div>

        <div className="space-y-1.5 max-w-md mx-auto">
          <h2 className="font-display text-lg sm:text-xl font-bold text-white tracking-tight">
            Pantau Perjalanan Busana Anda
          </h2>
          <p className="text-xs sm:text-sm text-slate-400 leading-relaxed">
            Masukkan nomor pesanan Malega Anda (<span className="font-mono text-[#CBAC70]">MLG-...</span>) pada kolom di atas untuk memantau status secara langsung.
          </p>
        </div>
      </div>

      {/* 3 Guidance Feature Cards */}
      <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div className="p-4 rounded-2xl bg-[#0B132B]/70 border border-white/5 space-y-2">
          <div className="w-8 h-8 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/20 text-[#CBAC70] flex items-center justify-center text-xs font-bold font-mono">
            01
          </div>
          <p className="font-bold text-xs text-white">Nomor Pesanan Resmi</p>
          <p className="text-[11px] text-slate-400 leading-relaxed">
            Tercantum pada email konfirmasi pesanan atau struk invoice pembayaran Anda.
          </p>
        </div>

        <div className="p-4 rounded-2xl bg-[#0B132B]/70 border border-white/5 space-y-2">
          <div className="w-8 h-8 rounded-xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center text-xs font-bold font-mono">
            02
          </div>
          <p className="font-bold text-xs text-white">Integrasi Biteship & JNE</p>
          <p className="text-[11px] text-slate-400 leading-relaxed">
            Pergerakan status paket dan penugasan kurir terhubung secara real-time.
          </p>
        </div>

        <div className="p-4 rounded-2xl bg-[#0B132B]/70 border border-white/5 space-y-2">
          <div className="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold font-mono">
            03
          </div>
          <p className="font-bold text-xs text-white">Bantuan WhatsApp CS</p>
          <p className="text-[11px] text-slate-400 leading-relaxed">
            Tim Concierge Malega siap membantu jika Anda membutuhkan informasi pesanan.
          </p>
        </div>
      </div>
    </div>
  );
}
