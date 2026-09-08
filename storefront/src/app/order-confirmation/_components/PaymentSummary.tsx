import React from 'react';
import Link from 'next/link';
import { Truck, MessageSquare, ArrowRight } from 'lucide-react';
import { formatRupiah } from '../../../lib/utils';

interface PaymentSummaryProps {
  order: any;
  waText: string;
}

export default function PaymentSummary({ order, waText }: PaymentSummaryProps) {
  return (
    <div className="lg:col-span-5 space-y-4 sm:space-y-6">
      <div className="luxury-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 space-y-3 sm:space-y-4 text-xs">
        <h3 className="font-bold text-xs uppercase tracking-wider text-[#CBAC70] border-b border-white/10 pb-2.5">
          Rincian Pembayaran
        </h3>

        <div className="space-y-1.5 sm:space-y-2 text-[#94A3B8]">
          <div className="flex justify-between">
            <span>Subtotal Produk</span>
            <span className="text-[#FDFCFF] font-semibold">
              {formatRupiah(order.subtotal || 0)}
            </span>
          </div>
          <div className="flex justify-between">
            <span>Ongkos Kirim</span>
            <span className="text-[#FDFCFF] font-semibold">
              {formatRupiah(order.shippingCost || 0)}
            </span>
          </div>
          {order.shippingDiscount > 0 && (
            <div className="flex justify-between text-emerald-400">
              <span>Diskon Ongkir</span>
              <span>-{formatRupiah(order.shippingDiscount)}</span>
            </div>
          )}
          {order.productDiscount > 0 && (
            <div className="flex justify-between text-[#CBAC70]">
              <span>Voucher Potongan</span>
              <span>-{formatRupiah(order.productDiscount)}</span>
            </div>
          )}
          <div className="flex justify-between">
            <span>Biaya Layanan</span>
            <span className="text-[#FDFCFF] font-semibold">
              {formatRupiah(order.serviceFee || 0)}
            </span>
          </div>

          <div className="border-t border-white/10 pt-2.5 flex justify-between items-baseline font-bold">
            <span className="text-[#FDFCFF] text-xs">Total Pembayaran:</span>
            <span className="text-lg sm:text-xl font-black text-[#CBAC70] gold-gradient-pure">
              {formatRupiah(order.total || 0)}
            </span>
          </div>
        </div>

        {/* Action Buttons: 1 Primary Full-Width + 2 Secondary Side-by-Side */}
        <div className="space-y-2 pt-1">
          {/* Live Tracking Portal Button (Primary) */}
          <Link
            href={`/track?q=${encodeURIComponent(order.invoiceNumber || '')}`}
            className="w-full py-2.5 sm:py-3 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-md shadow-[#CBAC70]/15 transition-all active:scale-98 cursor-pointer"
          >
            <Truck className="w-4 h-4 text-[#0B132B] shrink-0" />
            <span className="whitespace-nowrap">Lacak Pengiriman (Live Tracking)</span>
          </Link>

          {/* Secondary Actions (WhatsApp Admin & Katalog Toko) */}
          <div className="grid grid-cols-2 gap-2">
            <a
              href={`https://wa.me/6281234567890?text=${waText}`}
              target="_blank"
              rel="noopener noreferrer"
              className="py-2.5 px-2 bg-emerald-600/15 hover:bg-emerald-600/25 border border-emerald-500/30 text-emerald-400 rounded-xl font-bold text-[11px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-all active:scale-98"
            >
              <MessageSquare className="w-3.5 h-3.5 shrink-0" />
              <span className="truncate">WA Admin</span>
            </a>

            <Link
              href="/katalog"
              className="py-2.5 px-2 bg-[#111D42] hover:bg-[#172654] border border-[#CBAC70]/30 text-[#CBAC70] hover:text-[#E3CD99] rounded-xl font-bold text-[11px] uppercase tracking-wider flex items-center justify-center gap-1.5 transition-colors active:scale-98"
            >
              <span className="truncate">Koleksi Lain</span>
              <ArrowRight className="w-3.5 h-3.5 shrink-0" />
            </Link>
          </div>
        </div>
      </div>
    </div>
  );
}
