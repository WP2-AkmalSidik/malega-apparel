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
  // Strip nested parentheses or redundant service suffixes e.g. "SPX Express (Standard Delivery)" -> "SPX Express"
  const courierClean = (order.shipping?.courier || order.shipping?.name || 'Kurir')
    .replace(/\s*\([^)]*\)/g, '')
    .trim();

  // Status labels: "Di Kemas", "Di Kirim", "Tiba"
  const getShortStatus = () => {
    const rawStatus = (order.status || '').toLowerCase();
    if (rawStatus.includes('selesai') || rawStatus.includes('tiba') || rawStatus.includes('delivered')) {
      return {
        label: 'Tiba',
        color: 'text-emerald-400 bg-emerald-500/15 border-emerald-500/30',
        text: 'Pesanan Telah Tiba',
      };
    }
    if (order.hasActualResi || rawStatus.includes('kirim') || rawStatus.includes('transit')) {
      return {
        label: 'Di Kirim',
        color: 'text-sky-400 bg-sky-500/15 border-sky-500/30',
        text: 'Sedang Di Kirim',
      };
    }
    return {
      label: 'Di Kemas',
      color: 'text-amber-400 bg-amber-500/15 border-amber-500/30',
      text: 'Sedang Diproses Penjual',
    };
  };

  const shortStatus = getShortStatus();

  return (
    <div className="luxury-card rounded-2xl sm:rounded-3xl p-4 sm:p-8 md:p-10 text-center space-y-3.5 sm:space-y-6 shadow-2xl relative overflow-hidden border border-[#CBAC70]/40">
      {/* Glow */}
      <div className="absolute top-0 left-1/2 -translate-x-1/2 w-48 sm:w-64 h-48 sm:h-64 bg-[#CBAC70]/10 blur-[80px] sm:blur-[100px] rounded-full pointer-events-none" />

      {/* Success Badge Icon */}
      <div className="w-11 h-11 sm:w-14 sm:h-14 rounded-xl sm:rounded-2xl bg-[#CBAC70]/20 border border-[#CBAC70] text-[#CBAC70] flex items-center justify-center mx-auto shadow-lg">
        <CheckCircle2 className="w-6 h-6 sm:w-8 sm:h-8" />
      </div>

      {/* Hero Headings */}
      <div className="space-y-1 sm:space-y-2">
        <span className="text-[10px] sm:text-xs font-mono font-bold uppercase tracking-wider text-[#CBAC70] block">
          TRANSAKSI RESMI MALEGA APPAREL
        </span>
        <h1 className="text-xl sm:text-3xl font-black text-[#FDFCFF] uppercase tracking-tight leading-snug">
          Pesanan Berhasil Dikonfirmasi!
        </h1>
        <p className="text-[11px] sm:text-xs text-[#94A3B8] max-w-md mx-auto leading-relaxed">
          Terima kasih telah berbelanja. Invoice pesanan dan nomor resi pelacakan Anda telah
          diterbitkan secara otomatis.
        </p>
      </div>

      {/* Invoice Code & Resi Strip */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 sm:gap-4 max-w-xl mx-auto pt-1 sm:pt-2 text-xs">
        {/* Invoice Box */}
        <div className="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-[#080E20]/90 border border-[#CBAC70]/30 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-[#CBAC70] transition-all">
          <div className="flex items-center justify-between gap-2">
            <span className="text-[#94A3B8] text-[10px] sm:text-[11px] font-medium flex items-center gap-1.5 whitespace-nowrap min-w-0">
              <Receipt className="w-3.5 h-3.5 text-[#CBAC70] shrink-0" />
              <span className="truncate">Nomor Invoice:</span>
            </span>
            <span className="text-[9px] sm:text-[10px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1.5 py-0.5 rounded font-bold uppercase tracking-wider whitespace-nowrap shrink-0">
              {order.payment?.status || 'Lunas'}
            </span>
          </div>
          <div className="flex items-center justify-between gap-2">
            <span className="font-mono font-black text-[#FDFCFF] text-xs sm:text-sm tracking-tight truncate">
              {order.invoiceNumber}
            </span>
            <div className="flex items-center gap-1 shrink-0">
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
        <div className="p-3 sm:p-4 rounded-xl sm:rounded-2xl bg-[#080E20]/90 border border-white/10 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-white/25 transition-all">
          <div className="flex items-center justify-between gap-2">
            <span className="text-[#94A3B8] text-[10px] sm:text-[11px] font-medium flex items-center gap-1.5 whitespace-nowrap min-w-0">
              <Truck className="w-3.5 h-3.5 text-sky-400 shrink-0" />
              <span className="truncate">No. Resi ({courierClean}):</span>
            </span>
            <span
              className={`text-[9px] sm:text-[10px] border px-1.5 py-0.5 rounded font-bold uppercase tracking-wider whitespace-nowrap shrink-0 ${shortStatus.color}`}
            >
              {shortStatus.label}
            </span>
          </div>
          <div className="flex items-center justify-between gap-2">
            <span
              className={`font-mono font-bold truncate ${
                order.hasActualResi && order.trackingNumber
                  ? 'text-[#CBAC70] text-xs sm:text-sm'
                  : 'text-amber-300 font-sans text-xs'
              }`}
            >
              {order.hasActualResi && order.trackingNumber
                ? order.trackingNumber
                : shortStatus.text}
            </span>
            <button
              onClick={() => copyText(order.hasActualResi ? order.trackingNumber : '', 'resi')}
              className="p-1.5 rounded-lg bg-white/5 hover:bg-[#CBAC70]/20 text-[#94A3B8] hover:text-[#CBAC70] border border-white/10 hover:border-[#CBAC70]/40 transition-all active:scale-90 cursor-pointer shrink-0"
              title={order.hasActualResi ? 'Salin Nomor Resi' : 'Status Pengiriman'}
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
