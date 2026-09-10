'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { Package, Truck, ArrowRight, Star, Check } from 'lucide-react';
import { CustomerPastOrder } from '../../../types';
import { useAuth } from '../../../context/AuthContext';
import SubmitReviewModal from './SubmitReviewModal';

interface OrderHistoryProps {
  orders: CustomerPastOrder[];
  isLoadingOrders: boolean;
  onTrackOrder?: (orderNumber: string) => void;
}

export default function OrderHistory({
  orders: initialOrders,
  isLoadingOrders,
  onTrackOrder,
}: OrderHistoryProps) {
  const { token } = useAuth();
  const [orders, setOrders] = useState<CustomerPastOrder[]>(initialOrders);
  const [selectedReviewItem, setSelectedReviewItem] = useState<{
    productId: number;
    productName: string;
    orderId: number;
  } | null>(null);

  // Sync state if initialOrders changes
  React.useEffect(() => {
    setOrders(initialOrders);
  }, [initialOrders]);

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

  const handleReviewSuccess = () => {
    if (!selectedReviewItem) return;
    const reviewedProductId = selectedReviewItem.productId;

    // Optimistically update has_reviewed flag on order items
    setOrders((prev) =>
      prev.map((order) => ({
        ...order,
        items: order.items.map((item) =>
          item.product_id === reviewedProductId
            ? { ...item, has_reviewed: true }
            : item
        ),
      }))
    );
  };

  return (
    <>
      <div className="space-y-4">
        {orders.map((order) => {
          const isPaidOrCompleted =
            order.status === 'paid' ||
            order.status === 'delivered' ||
            order.status === 'completed';

          return (
            <div
              key={order.id}
              className="rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-6 shadow-xl space-y-4"
            >
              <div className="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-white/5 gap-2">
                <div>
                  <div className="flex items-center gap-2">
                    <span className="font-mono font-bold text-sm text-slate-100">
                      {order.order_number}
                    </span>
                    <span className="text-xs text-slate-400 font-mono">
                      • {order.created_at}
                    </span>
                  </div>
                </div>

                <div className="flex items-center gap-3">
                  <span
                    className={`px-2.5 py-1 rounded-full text-xs font-bold font-mono ${
                      isPaidOrCompleted
                        ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
                        : order.status === 'shipped'
                        ? 'bg-blue-500/15 text-blue-400 border border-blue-500/30'
                        : 'bg-amber-500/15 text-amber-400 border border-amber-500/30'
                    }`}
                  >
                    {order.status_label || order.status.toUpperCase()}
                  </span>

                  {onTrackOrder ? (
                    <button
                      type="button"
                      onClick={() => onTrackOrder(order.order_number)}
                      className="px-3 py-1 rounded-xl bg-[#CBAC70]/10 hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/30 text-xs font-bold transition flex items-center gap-1.5 cursor-pointer active:scale-95 shadow-sm"
                    >
                      <Truck className="w-3.5 h-3.5" />
                      <span>Lacak Paket</span>
                    </button>
                  ) : (
                    <Link
                      href={`/track?order=${order.order_number}`}
                      className="px-3 py-1 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 text-xs font-bold text-slate-200 transition flex items-center gap-1"
                    >
                      <Truck className="w-3.5 h-3.5 text-[#CBAC70]" />
                      <span>Lacak Pengiriman</span>
                    </Link>
                  )}
                </div>
              </div>

              {/* Item List with Verified Review Action */}
              <div className="space-y-2.5">
                {order.items.map((item, idx) => (
                  <div
                    key={idx}
                    className="flex flex-col sm:flex-row sm:items-center justify-between text-xs py-2 border-b border-white/5 last:border-0 gap-2"
                  >
                    <div>
                      <p className="font-semibold text-slate-200">
                        {item.product_name || item.title}
                      </p>
                      <p className="text-[10px] text-slate-400 font-mono mt-0.5">
                        Varian: {item.title} • SKU: {item.sku} • {item.quantity} pcs
                      </p>
                    </div>

                    <div className="flex items-center gap-3 self-end sm:self-auto">
                      <p className="font-mono font-bold text-slate-300">
                        Rp {item.subtotal.toLocaleString('id-ID')}
                      </p>

                      {/* Verified Buyer Review Button */}
                      {isPaidOrCompleted && item.product_id && (
                        item.has_reviewed ? (
                          <span className="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30 flex items-center gap-1">
                            <Check className="w-3 h-3" />
                            <span>Sudah Diulas</span>
                          </span>
                        ) : (
                          <button
                            type="button"
                            onClick={() =>
                              setSelectedReviewItem({
                                productId: item.product_id!,
                                productName: item.product_name || item.title,
                                orderId: order.id,
                              })
                            }
                            className="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-[#CBAC70]/15 hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] border border-[#CBAC70]/40 transition active:scale-95 cursor-pointer flex items-center gap-1 shadow-sm"
                            title="Berikan rating dan ulasan untuk artikel ini"
                          >
                            <Star className="w-3 h-3 fill-current text-[#CBAC70]" />
                            <span>Beri Ulasan</span>
                          </button>
                        )
                      )}
                    </div>
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
                  <span className="text-[10px] text-slate-400 uppercase font-mono mr-2">
                    Total Belanja:
                  </span>
                  <span className="text-base font-bold font-mono text-[#CBAC70]">
                    {order.formatted_total}
                  </span>
                </div>
              </div>
            </div>
          );
        })}
      </div>

      {/* Review Submission Modal */}
      {selectedReviewItem && (
        <SubmitReviewModal
          isOpen={true}
          onClose={() => setSelectedReviewItem(null)}
          productId={selectedReviewItem.productId}
          productName={selectedReviewItem.productName}
          orderId={selectedReviewItem.orderId}
          token={token}
          onSuccess={handleReviewSuccess}
        />
      )}
    </>
  );
}
