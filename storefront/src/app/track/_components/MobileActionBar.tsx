import React from 'react';
import { MessageSquare, ExternalLink } from 'lucide-react';
import { LiveTrackingOrder } from '../../../types';

interface MobileActionBarProps {
  order: LiveTrackingOrder;
  waText: string;
  isGeneratingInvoice: boolean;
  onPayNow: (orderNumber: string) => void;
}

export default function MobileActionBar({ order, waText, isGeneratingInvoice, onPayNow }: MobileActionBarProps) {
  return (
    <div className="sm:hidden fixed bottom-0 inset-x-0 p-3 bg-[#070C1A]/95 backdrop-blur-xl border-t border-white/10 z-40 flex items-center gap-2">
      {order.paymentStatus?.code === 'unpaid' ? (
        <button
          type="button"
          onClick={() =>
            order.payment?.payment_url
              ? (window.location.href = order.payment.payment_url)
              : onPayNow(order.orderNumber)
          }
          className="flex-1 py-3 px-4 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-bold text-xs shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center gap-2 active:scale-95"
        >
          <span>Bayar {order.pricing.formatted_grand_total}</span>
          <ExternalLink className="w-3.5 h-3.5" />
        </button>
      ) : (
        <a
          href={`https://wa.me/6281234567890?text=${waText}`}
          target="_blank"
          rel="noopener noreferrer"
          className="flex-1 py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 flex items-center justify-center gap-2 active:scale-95"
        >
          <MessageSquare className="w-4 h-4" />
          <span>Bantuan CS WhatsApp</span>
        </a>
      )}
    </div>
  );
}
