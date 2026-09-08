import React from 'react';
import { ShoppingBag, ShieldCheck, Sparkles } from 'lucide-react';
import { LiveTrackingOrder } from '../../../types';
import { formatRupiah } from '../../../lib/utils';
import { parseItemDetails } from '../_lib/parse-item-details';

interface PackageTabProps {
  order: LiveTrackingOrder;
}

export default function PackageTab({ order }: PackageTabProps) {
  return (
    <div className="space-y-4">
      <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 space-y-4">
        <div className="flex items-center justify-between">
          <h3 className="font-mono text-xs uppercase font-bold text-[#CBAC70] tracking-wider flex items-center gap-2">
            <ShoppingBag className="w-3.5 h-3.5" />
            <span>Daftar Busana Pesanan</span>
          </h3>
          <span className="text-[11px] text-slate-400 font-mono">
            {order.items.reduce((acc, it) => acc + it.quantity, 0)} Item Produk
          </span>
        </div>

        <div className="divide-y divide-white/5">
          {order.items.map((item, idx) => {
            const details = parseItemDetails(item.product_name, item.variant_title, item.sku);

            return (
              <div
                key={idx}
                className="py-4 first:pt-1 last:pb-1 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
              >
                <div className="flex items-center gap-3.5 w-full sm:w-auto">
                  {/* Real Product Artwork Thumbnail */}
                  <div className="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl overflow-hidden bg-slate-900 border border-white/10 shrink-0 relative">
                    <img
                      src={details.image}
                      alt={details.title}
                      className="w-full h-full object-cover object-center"
                      onError={(e) => {
                        (e.target as HTMLElement).style.display = 'none';
                      }}
                    />
                  </div>

                  {/* Clean Micro Details */}
                  <div className="min-w-0 flex-1 space-y-1">
                    <p className="font-bold text-sm text-white leading-snug line-clamp-2">
                      {details.title}
                    </p>
                    <div className="flex flex-wrap items-center gap-1.5 text-[11px]">
                      {details.color && (
                        <span className="px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-slate-300 font-medium">
                          {details.color}
                        </span>
                      )}
                      {details.size && (
                        <span className="px-2 py-0.5 rounded-md bg-white/5 border border-white/10 text-slate-300 font-medium">
                          {details.size}
                        </span>
                      )}
                    </div>
                    <p className="font-mono text-[10px] text-slate-400 truncate">
                      SKU: <span className="text-[#CBAC70]">{item.sku}</span>
                    </p>
                  </div>
                </div>

                {/* Quantity & Subtotal */}
                <div className="w-full sm:w-auto flex items-center justify-between sm:flex-col sm:items-end gap-1 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/5">
                  <span className="text-xs text-slate-400">
                    {item.quantity} x {item.formatted_unit_price || formatRupiah(item.unit_price)}
                  </span>
                  <span className="font-mono font-bold text-sm text-[#CBAC70]">
                    {item.formatted_subtotal || formatRupiah(item.subtotal)}
                  </span>
                </div>
              </div>
            );
          })}
        </div>
      </div>

      {/* Quality & Packaging Badges */}
      <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <div className="p-4 rounded-2xl bg-[#0B132B] border border-white/10 space-y-2 text-xs">
          <p className="font-mono text-[11px] font-bold text-[#CBAC70] uppercase">Jaminan Kualitas Atelier</p>
          <div className="space-y-1.5 text-slate-300">
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>100% Busana Asli Malega Apparel Bespoke</span>
            </div>
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>Melewati Quality Control Jahitan & Kancing Presisi</span>
            </div>
            <div className="flex items-center gap-2">
              <ShieldCheck className="w-4 h-4 text-emerald-400 shrink-0" />
              <span>Garansi Penukaran Ukuran dalam 7 Hari</span>
            </div>
          </div>
        </div>

        <div className="p-4 rounded-2xl bg-[#0B132B] border border-white/10 space-y-2 text-xs">
          <p className="font-mono text-[11px] font-bold text-[#CBAC70] uppercase">Standar Kemasan Mewah</p>
          <div className="space-y-1.5 text-slate-300">
            <div className="flex items-center gap-2">
              <Sparkles className="w-4 h-4 text-amber-400 shrink-0" />
              <span>Exclusive Black Gift Box + Silk Ribbon</span>
            </div>
            <div className="flex items-center gap-2">
              <Sparkles className="w-4 h-4 text-amber-400 shrink-0" />
              <span>Dust Bag Pelindung Serat Kain Premium</span>
            </div>
            <div className="flex items-center gap-2">
              <Sparkles className="w-4 h-4 text-amber-400 shrink-0" />
              <span>Segel Keamanan Hologram Anti-Buka</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
