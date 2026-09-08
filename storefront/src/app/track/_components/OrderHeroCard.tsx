import React from 'react';
import { Truck, Clock, Copy, Check, ExternalLink } from 'lucide-react';
import { LiveTrackingOrder } from '../../../types';

interface OrderHeroCardProps {
  order: LiveTrackingOrder;
  progressStep: number;
  courierCompany: string;
  copiedKey: string | null;
  isGeneratingInvoice: boolean;
  onCopy: (text: string, key: string, label?: string) => void;
  onPayNow: (orderNumber: string) => void;
}

export default function OrderHeroCard({
  order,
  progressStep,
  courierCompany,
  copiedKey,
  isGeneratingInvoice,
  onCopy,
  onPayNow,
}: OrderHeroCardProps) {
  const steps = [
    { step: 1, label: 'Dipesan' },
    { step: 2, label: 'Diproses' },
    { step: 3, label: 'Siap Kirim' },
    { step: 4, label: 'Dikirim' },
    { step: 5, label: 'Terkirim' },
  ];

  const stepDescriptions: Record<number, string> = {
    1: 'Tahap 1: Pesanan baru saja dibuat dan menunggu verifikasi.',
    2: 'Tahap 2: Busana sedang dipacking dan melewati Quality Control Malega.',
    3: 'Tahap 3: Busana selesai dikemas dan siap diserahkan kepada kurir ekspedisi.',
    4: 'Tahap 4: Paket sedang dalam perjalanan antar-hub logistik.',
    5: 'Tahap 5: Paket telah sampai dan diterima oleh pelanggan.',
  };

  return (
    <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 shadow-2xl relative overflow-hidden space-y-5">
      <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-80" />

      {/* Order Number & Live Badges */}
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div className="space-y-1">
          <div className="flex items-center gap-2">
            <span className="text-xs font-mono uppercase tracking-wider text-slate-400">Nomor Pesanan</span>
            <button
              type="button"
              onClick={() => onCopy(order.orderNumber, 'order', 'Nomor Pesanan')}
              className="p-1 rounded-md hover:bg-white/10 text-slate-400 hover:text-[#CBAC70] transition-colors cursor-pointer"
              title="Salin Nomor Pesanan"
            >
              {copiedKey === 'order' ? (
                <span className="text-[10px] text-emerald-400 font-mono font-bold">✓ Tersalin</span>
              ) : (
                <Copy className="w-3.5 h-3.5" />
              )}
            </button>
          </div>
          <p className="font-mono font-bold text-xl sm:text-2xl text-[#CBAC70] tracking-tight">
            {order.orderNumber}
          </p>
        </div>

        <div className="flex flex-wrap items-center gap-2">
          <span className="px-3 py-1 rounded-full text-xs font-bold bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30">
            {order.orderStatus.label}
          </span>
          <span
            className={`px-3 py-1 rounded-full text-xs font-bold border ${
              order.paymentStatus.code === 'paid'
                ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30'
                : 'bg-amber-500/15 text-amber-400 border-amber-500/30 animate-pulse'
            }`}
          >
            {order.paymentStatus.label}
          </span>
        </div>
      </div>

      {/* Kurir & Detail Singkat */}
      <div className="pt-4 border-t border-white/5 flex flex-wrap items-center justify-between gap-3 text-xs text-slate-300">
        <div className="flex items-center gap-2">
          <Truck className="w-4 h-4 text-[#CBAC70]" />
          <span>
            Ekspedisi: <strong className="text-white">{courierCompany}</strong>
          </span>
        </div>
        <div className="flex items-center gap-2 text-slate-400">
          <Clock className="w-3.5 h-3.5" />
          <span>
            {new Date(order.createdAt).toLocaleString('id-ID', {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit',
            })}{' '}
            WIB
          </span>
        </div>
      </div>

      {/* Progress Stepper Line */}
      <div className="pt-4 border-t border-white/5 space-y-3">
        <div className="relative">
          <div className="absolute top-4 -translate-y-1/2 left-4 right-4 sm:left-6 sm:right-6 h-1 bg-[#14204A] rounded-full overflow-hidden">
            <div
              className="h-full bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] transition-all duration-700"
              style={{ width: `${((progressStep - 1) / 4) * 100}%` }}
            />
          </div>

          <div className="relative flex justify-between">
            {steps.map((st) => (
              <div key={st.step} className="flex flex-col items-center gap-2">
                <div
                  className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all relative z-10 ring-4 ring-[#0B132B] ${
                    progressStep >= st.step
                      ? 'bg-[#CBAC70] text-[#060913] shadow-[0_0_14px_rgba(203,172,112,0.7)]'
                      : 'bg-[#0B132B] text-slate-500 border border-slate-700'
                  }`}
                >
                  {progressStep > st.step ? (
                    <Check className="w-3.5 h-3.5 stroke-[3]" />
                  ) : (
                    st.step
                  )}
                </div>
                <span
                  className={`text-[10px] sm:text-xs font-medium text-center ${
                    progressStep >= st.step ? 'text-white font-semibold' : 'text-slate-500'
                  }`}
                >
                  {st.label}
                </span>
              </div>
            ))}
          </div>
        </div>

        {/* Subtitle Step Indicator */}
        <div className="text-center pt-1">
          <p className="text-xs text-[#CBAC70] font-medium">
            {stepDescriptions[progressStep] || ''}
          </p>
        </div>
      </div>

      {/* Unpaid Alert Card */}
      {order.paymentStatus?.code === 'unpaid' && (
        <div className="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
          <div className="space-y-0.5">
            <div className="flex items-center gap-2 text-amber-400 font-bold text-xs sm:text-sm">
              <span className="w-2 h-2 rounded-full bg-amber-400 animate-ping" />
              <span>Menunggu Pembayaran</span>
            </div>
            <p className="text-xs text-slate-300">
              Tagihan: <strong className="font-mono text-[#CBAC70]">{order.pricing.formatted_grand_total}</strong>
            </p>
          </div>
          <button
            type="button"
            onClick={() =>
              order.payment?.payment_url
                ? (window.location.href = order.payment.payment_url)
                : onPayNow(order.orderNumber)
            }
            disabled={isGeneratingInvoice}
            className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-bold text-xs shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center gap-2 active:scale-95 transition-all"
          >
            <span>{isGeneratingInvoice ? 'Memuat Gateway...' : '⚡ Bayar Sekarang (Duitku)'}</span>
            <ExternalLink className="w-3.5 h-3.5" />
          </button>
        </div>
      )}
    </div>
  );
}
