'use client';

import React from 'react';
import Link from 'next/link';
import { Package, Truck, ArrowRight } from 'lucide-react';
import { CustomerPastOrder } from '../../../types';

interface OrderHistoryProps {
  orders: CustomerPastOrder[];
  isLoadingOrders: boolean;
}

export default function OrderHistory({ orders, isLoadingOrders }: OrderHistoryProps) {
  if (isLoadingOrders) {
    return (
      <div className="py-12 text-center text-slate-400 font-mono text-xs">
        Memuat riwayat pesanan...
      </div>
    );
  }

  if (orders.length === 0) {
    return (
      <div className="py-16 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-3">
        <Package className="w-12 h-12 text-slate-500 mx-auto" />
        <p className="text-sm font-bold text-slate-300">Belum Ada Riwayat Pesanan</p>
        <p className="text-xs text-slate-500">Mulai belanja artikel streetwear favorit Anda sekarang.</p>
        <Link
          href="/katalog"
          className="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs shadow hover:bg-[#E3CD99] transition"
        >
          <span>Mulai Belanja</span>
          <ArrowRight className="w-4 h-4" />
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-4">
      {orders.map((order) => (
        <div
          key={order.id}
          className="rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-6 shadow-xl space-y-4"
        >
          <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-white/5 gap-2">
            <div>
              <div className="flex items-center gap-2">
                <span className="font-mono font-bold text-sm text-slate-100">{order.order_number}</span>
                <span className="text-xs text-slate-400 font-mono">• {order.created_at}</span>
              </div>
            </div>

            <div className="flex items-center gap-3">
              <span
                className={`px-2.5 py-1 rounded-full text-xs font-bold font-mono ${
                  order.status === 'paid' || order.status === 'delivered'
                    ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
                    : order.status === 'shipped'
                    ? 'bg-blue-500/15 text-blue-400 border border-blue-500/30'
                    : 'bg-amber-500/15 text-amber-400 border border-amber-500/30'
                }`}
              >
                {order.status_label || order.status.toUpperCase()}
              </span>

              <Link
                href={`/track?order=${order.order_number}`}
                className="px-3 py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition flex items-center gap-1"
              >
                <Truck className="w-3.5 h-3.5 text-[#CBAC70]" />
                <span>Lacak Pengiriman</span>
              </Link>
            </div>
          </div>

          {/* Item List */}
          <div className="space-y-2">
            {order.items.map((item, idx) => (
              <div key={idx} className="flex items-center justify-between text-xs py-1">
                <div>
                  <p className="font-semibold text-slate-200">{item.title}</p>
                  <p className="text-[10px] text-slate-400 font-mono">
                    SKU: {item.sku} • {item.quantity} pcs
                  </p>
                </div>
                <p className="font-mono font-bold text-slate-300">
                  Rp {item.subtotal.toLocaleString('id-ID')}
                </p>
              </div>
            ))}
          </div>

          {/* Total & Courier */}
          <div className="pt-3 border-t border-white/5 flex items-center justify-between">
            <div>
              {order.shipping?.courier && (
                <p className="text-xs text-slate-400 font-mono">
                  Kurir: <span className="text-slate-200">{order.shipping.courier}</span> (
                  {order.shipping.waybill || 'Sedang diproses'})
                </p>
              )}
            </div>
            <div className="text-right">
              <span className="text-[10px] text-slate-400 uppercase font-mono mr-2">Total Belanja:</span>
              <span className="text-base font-bold font-mono text-[#CBAC70]">
                {order.formatted_total}
              </span>
            </div>
          </div>
        </div>
      ))}
    </div>
  );
}
