import React from 'react';
import Link from 'next/link';
import { CheckCircle2, Receipt, Copy, Check, ExternalLink, Truck } from 'lucide-react';

interface ConfirmationHeroProps {
  order: any;
  copiedInvoice: boolean;
  copiedResi: boolean;
  copyText: (text: string, type: 'invoice' | 'resi') => void;
}

export default function ConfirmationHero({
  order,
  copiedInvoice,
  copiedResi,
  copyText,
}: ConfirmationHeroProps) {
  return (
    <div className="luxury-card rounded-3xl p-8 sm:p-12 text-center space-y-6 shadow-2xl relative overflow-hidden border border-[#CBAC70]/40">
      {/* Glow */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-[#CBAC70]/10 blur-[100px] rounded-full pointer-events-none" />

      <div className="w-16 h-16 rounded-2xl bg-[#CBAC70]/20 border border-[#CBAC70] text-[#CBAC70] flex items-center justify-center mx-auto shadow-lg">
        <CheckCircle2 className="w-9 h-9" />
      </div>

      <div className="space-y-2">
        <span className="text-xs font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
          TRANSAKSI RESMI MALEGA APPAREL
        </span>
        <h1 className="text-3xl sm:text-4xl font-black text-[#FDFCFF] uppercase tracking-tight">
          Pesanan Berhasil Dikonfirmasi!
        </h1>
        <p className="text-xs sm:text-sm text-[#94A3B8] max-w-md mx-auto leading-relaxed">
          Terima kasih telah berbelanja. Invoice pesanan dan nomor resi pelacakan Anda telah
          diterbitkan secara otomatis.
        </p>
      </div>

      {/* Invoice Code & Resi Strip */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl mx-auto pt-2 text-xs">
        {/* Invoice Box */}
        <div className="p-4 rounded-2xl bg-[#080E20]/90 border border-[#CBAC70]/30 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-[#CBAC70] transition-all">
          <div className="flex items-center justify-between">
            <span className="text-[#94A3B8] text-[11px] font-medium flex items-center gap-1.5">
              <Receipt className="w-3.5 h-3.5 text-[#CBAC70]" />
              Nomor Invoice:
            </span>
            <span className="text-[10px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1.5 py-0.5 rounded font-medium">
              {order.payment?.status || 'Lunas'}
            </span>
          </div>
          <div className="flex items-center justify-between gap-2">
            <span className="font-mono font-black text-[#FDFCFF] text-sm tracking-tight truncate">
              {order.invoiceNumber}
            </span>
            <div className="flex items-center gap-1">
              <button
                onClick={() => copyText(order.invoiceNumber, 'invoice')}
                className="p-1.5 rounded-lg bg-white/5 hover:bg-[#CBAC70]/20 text-[#94A3B8] hover:text-[#CBAC70] border border-white/10 hover:border-[#CBAC70]/40 transition-all active:scale-90 cursor-pointer"
                title="Salin Nomor Invoice"
              >
                {copiedInvoice ? (
                  <Check className="w-3.5 h-3.5 text-emerald-400" />
                ) : (
                  <Copy className="w-3.5 h-3.5" />
                )}
              </button>
              <Link
                href={`/track?q=${encodeURIComponent(order.invoiceNumber)}`}
                className="p-1.5 rounded-lg bg-[#CBAC70]/10 hover:bg-[#CBAC70]/25 text-[#CBAC70] border border-[#CBAC70]/30 transition-all active:scale-90"
                title="Lacak dengan Invoice"
              >
                <ExternalLink className="w-3.5 h-3.5" />
              </Link>
            </div>
          </div>
        </div>

        {/* Resi Box */}
        <div className="p-4 rounded-2xl bg-[#080E20]/90 border border-white/10 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-white/25 transition-all">
          <div className="flex items-center justify-between">
            <span className="text-[#94A3B8] text-[11px] font-medium flex items-center gap-1.5">
              <Truck className="w-3.5 h-3.5 text-sky-400" />
              No. Resi ({order.shipping?.courier || 'Kurir'}):
            </span>
            {order.hasActualResi ? (
              <span className="text-[10px] text-sky-400 bg-sky-500/10 border border-sky-500/20 px-1.5 py-0.5 rounded font-medium">
                Aktif
              </span>
            ) : (
              <span className="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded font-medium">
                Proses Kemas
              </span>
            )}
          </div>
          <div className="flex items-center justify-between gap-2">
            <span
              className={`font-mono text-xs sm:text-sm font-bold truncate ${
                order.hasActualResi ? 'text-[#CBAC70]' : 'text-slate-400'
              }`}
            >
              {order.trackingNumber}
            </span>
            <button
              onClick={() => copyText(order.trackingNumber, 'resi')}
              className="p-1.5 rounded-lg bg-white/5 hover:bg-[#CBAC70]/20 text-[#94A3B8] hover:text-[#CBAC70] border border-white/10 hover:border-[#CBAC70]/40 transition-all active:scale-90 cursor-pointer"
              title="Salin Nomor Resi"
            >
              {copiedResi ? (
                <Check className="w-3.5 h-3.5 text-emerald-400" />
              ) : (
                <Copy className="w-3.5 h-3.5" />
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
