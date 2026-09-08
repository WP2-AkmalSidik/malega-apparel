import React from 'react';
import { FileText } from 'lucide-react';
import { formatRupiah } from '../../../lib/utils';

interface OrderedItemsProps {
  order: any;
}

export default function OrderedItems({ order }: OrderedItemsProps) {
  const courierClean = (order.shipping?.name || order.shipping?.courier || 'Kurir')
    .replace(/\s*\([^)]*\)/g, '')
    .trim();

  return (
    <div className="lg:col-span-7 luxury-card rounded-2xl sm:rounded-3xl p-4 sm:p-6 space-y-3 sm:space-y-4 text-xs">
      <h3 className="font-bold text-xs uppercase tracking-wider text-[#CBAC70] border-b border-white/10 pb-2.5 flex items-center gap-2">
        <FileText className="w-4 h-4" /> Rincian Item yang Dipesan
      </h3>

      <div className="divide-y divide-white/5 space-y-2">
        {(order.items || []).map((item: any, idx: number) => (
          <div key={idx} className="pt-2 flex items-center justify-between gap-3">
            <div className="flex items-center gap-2.5 min-w-0">
              <img
                src={item.image}
                alt={item.title}
                className="w-11 h-13 rounded-xl object-cover border border-white/10 shrink-0"
              />
              <div className="min-w-0">
                <h4 className="font-bold text-[#FDFCFF] truncate">{item.title}</h4>
                <p className="text-[10px] sm:text-[11px] text-[#94A3B8]">
                  {item.color} • Size {item.size} (x{item.quantity})
                </p>
              </div>
            </div>
            <span className="font-black text-[#CBAC70] text-xs sm:text-sm whitespace-nowrap shrink-0">
              {formatRupiah(item.price * item.quantity)}
            </span>
          </div>
        ))}
      </div>

      <div className="pt-3 border-t border-white/10 text-[#94A3B8] text-[11px] space-y-1">
        <p>
          <strong className="text-white">Alamat Pengiriman:</strong> {order.address?.name} (
          {order.address?.phone}) - {order.address?.street}, {order.address?.city},{' '}
          {order.address?.postalCode}
        </p>
        <p>
          <strong className="text-white">Metode Pembayaran:</strong> {order.payment?.name}
        </p>
        <p>
          <strong className="text-white">Layanan Kurir:</strong> {courierClean}
        </p>
      </div>
    </div>
  );
}
