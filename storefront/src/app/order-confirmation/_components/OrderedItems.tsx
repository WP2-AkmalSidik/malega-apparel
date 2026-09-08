import React from 'react';
import { FileText } from 'lucide-react';
import { formatRupiah } from '../../../lib/utils';

interface OrderedItemsProps {
  order: any;
}

export default function OrderedItems({ order }: OrderedItemsProps) {
  return (
    <div className="lg:col-span-7 luxury-card rounded-3xl p-6 space-y-4 text-xs">
      <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3 flex items-center gap-2">
        <FileText className="w-4 h-4" /> Rincian Item yang Dipesan
      </h3>

      <div className="divide-y divide-white/5 space-y-2">
        {(order.items || []).map((item: any, idx: number) => (
          <div key={idx} className="pt-2.5 flex items-center justify-between gap-3">
            <div className="flex items-center gap-3">
              <img
                src={item.image}
                alt={item.title}
                className="w-12 h-14 rounded-xl object-cover border border-white/10 shrink-0"
              />
              <div>
                <h4 className="font-bold text-[#FDFCFF]">{item.title}</h4>
                <p className="text-[11px] text-[#94A3B8]">
                  {item.color} • Size {item.size} (x{item.quantity})
                </p>
              </div>
            </div>
            <span className="font-black text-[#CBAC70] text-sm">
              {formatRupiah(item.price * item.quantity)}
            </span>
          </div>
        ))}
      </div>

      <div className="pt-4 border-t border-white/10 text-[#94A3B8] text-[11px] space-y-1">
        <p>
          <strong className="text-white">Alamat Pengiriman:</strong> {order.address?.name} (
          {order.address?.phone}) - {order.address?.street}, {order.address?.city},{' '}
          {order.address?.postalCode}
        </p>
        <p>
          <strong className="text-white">Metode Pembayaran:</strong> {order.payment?.name}
        </p>
        <p>
          <strong className="text-white">Layanan Kurir:</strong> {order.shipping?.name}
        </p>
      </div>
    </div>
  );
}
