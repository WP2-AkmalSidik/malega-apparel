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
    <div className="lg:col-span-5 space-y-6">
      <div className="luxury-card rounded-3xl p-6 space-y-4 text-xs">
        <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3">
          Rincian Pembayaran
        </h3>

        <div className="space-y-2 text-[#94A3B8]">
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

          <div className="border-t border-white/10 pt-3 flex justify-between items-baseline font-bold">
            <span className="text-[#FDFCFF]">Total Pembayaran:</span>
            <span className="text-2xl font-black text-[#CBAC70] gold-gradient-pure">
              {formatRupiah(order.total || 0)}
            </span>
          </div>
        </div>

        {/* Live Tracking Portal Button */}
        <Link
          href={`/track?q=${encodeURIComponent(order.invoiceNumber || '')}`}
          className="w-full py-4 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-[#CBAC70]/20 transition-all active:scale-98"
        >
          <Truck className="w-4 h-4 text-[#0B132B]" />
          <span>Lacak Pengiriman Paket (Live Tracking)</span>
        </Link>

        {/* WhatsApp Notification Button */}
        <a
          href={`https://wa.me/6281234567890?text=${waText}`}
          target="_blank"
          rel="noopener noreferrer"
          className="w-full py-3.5 bg-emerald-600/90 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg transition-all active:scale-98"
        >
          <MessageSquare className="w-4 h-4" />
          <span>Konfirmasi via WhatsApp Admin</span>
        </a>

        <Link
          href="/katalog"
          className="w-full py-3.5 bg-[#111D42] hover:bg-[#172654] border border-[#CBAC70]/30 text-[#CBAC70] rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-colors"
        >
          <span>Belanja Koleksi Lainnya</span>
          <ArrowRight className="w-3.5 h-3.5" />
        </Link>
      </div>
    </div>
  );
}
