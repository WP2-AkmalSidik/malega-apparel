import React from 'react';
import { Printer } from 'lucide-react';
import { LiveTrackingOrder } from '../../../types';
import { formatRupiah } from '../../../lib/utils';

interface InvoiceTabProps {
  order: LiveTrackingOrder;
  courierCompany: string;
}

export default function InvoiceTab({ order, courierCompany }: InvoiceTabProps) {
  // Mathematical Financial Breakdown (100% Klop dengan Tagihan Nyata)
  const subtotal = order.pricing.subtotal || 0;
  const shipping = order.pricing.shipping_total || 0;
  const discount = order.pricing.discount_total || 0;
  const grandTotal = order.pricing.grand_total || 0;
  // Explicit service fee or calculated delta to guarantee 100% mathematical consistency
  const serviceFee =
    order.pricing.service_fee && order.pricing.service_fee > 0
      ? order.pricing.service_fee
      : Math.max(0, grandTotal - (subtotal + shipping - discount));

  return (
    <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 space-y-5 shadow-2xl">
      <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-white/10 gap-2">
        <div>
          <p className="font-display font-bold text-base text-white">Rincian Faktur & Pembayaran</p>
          <p className="text-xs text-slate-400">
            Waktu Transaksi:{' '}
            {new Date(order.createdAt).toLocaleDateString('id-ID', {
              day: 'numeric',
              month: 'long',
              year: 'numeric',
            })}
          </p>
        </div>
        <span
          className={`self-start sm:self-auto px-3 py-1 rounded-full text-xs font-bold border ${
            order.paymentStatus.code === 'paid'
              ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30'
              : 'bg-amber-500/15 text-amber-400 border-amber-500/30'
          }`}
        >
          {order.paymentStatus.label}
        </span>
      </div>

      {/* Financial Breakdown */}
      <div className="space-y-2.5 text-xs">
        <div className="flex justify-between text-slate-300">
          <span>Subtotal Produk ({order.items.reduce((acc, it) => acc + it.quantity, 0)} Pcs)</span>
          <span className="font-mono text-white">{formatRupiah(subtotal)}</span>
        </div>

        <div className="flex justify-between text-slate-300">
          <span>Ongkos Kirim ({courierCompany})</span>
          <span className="font-mono text-white">{formatRupiah(shipping)}</span>
        </div>

        {discount > 0 && (
          <div className="flex justify-between text-rose-400">
            <span>Potongan Diskon Promo</span>
            <span className="font-mono font-semibold">
              -Rp {discount.toLocaleString('id-ID')}
            </span>
          </div>
        )}

        {serviceFee > 0 && (
          <div className="flex justify-between text-slate-300">
            <span>Biaya Layanan Sistem</span>
            <span className="font-mono text-white">{formatRupiah(serviceFee)}</span>
          </div>
        )}

        <div className="pt-3 border-t border-white/10 flex justify-between items-center text-sm">
          <span className="font-bold text-white">Total Tagihan</span>
          <span className="font-mono font-bold text-base text-[#CBAC70]">
            {formatRupiah(grandTotal)}
          </span>
        </div>
      </div>

      {/* Payment Gateway Information */}
      {order.payment && (
        <div className="p-4 rounded-2xl bg-[#060913] border border-white/5 space-y-2 text-xs">
          <p className="font-mono text-[11px] font-bold text-[#CBAC70] uppercase">Info Transaksi Duitku</p>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-2 text-slate-300">
            <div>
              <span className="text-slate-400 text-[11px]">Kanal Pembayaran:</span>
              <p className="font-medium text-white">{order.payment.payment_method_name || 'Online Payment'}</p>
            </div>
            {order.payment.reference && (
              <div>
                <span className="text-slate-400 text-[11px]">No. Referensi:</span>
                <p className="font-mono font-medium text-slate-200">{order.payment.reference}</p>
              </div>
            )}
            {order.payment.paid_at && (
              <div>
                <span className="text-slate-400 text-[11px]">Waktu Pelunasan:</span>
                <p className="font-mono text-emerald-400">
                  {new Date(order.payment.paid_at).toLocaleString('id-ID')} WIB
                </p>
              </div>
            )}
          </div>
        </div>
      )}

      {/* Print Button */}
      <div className="pt-2 flex justify-end">
        <button
          type="button"
          onClick={() => window.print()}
          className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-200 text-xs font-semibold transition-all flex items-center gap-2"
        >
          <Printer className="w-3.5 h-3.5 text-[#CBAC70]" />
          <span>Cetak / Simpan PDF Faktur</span>
        </button>
      </div>
    </div>
  );
}
