'use client';

import React from 'react';
import { MessageSquare, ExternalLink } from 'lucide-react';
import { LiveTrackingOrder } from '../../../types';

interface MobileActionBarProps {
  order: LiveTrackingOrder;
  waText: string;
  isGeneratingInvoice: boolean;
  onPayNow: (orderNumber: string) => void;
}

export default function MobileActionBar({
  order,
  waText,
  isGeneratingInvoice,
  onPayNow,
}: MobileActionBarProps) {
  const isUnpaid = order.paymentStatus?.code === 'unpaid';

  return (
    <>
      {/* 1. Unpaid Sticky Payment Bar (Sits cleanly ABOVE the mobile bottom navigation bar, without covering it) */}
      {isUnpaid && (
        <div className="lg:hidden fixed bottom-16 inset-x-0 p-2.5 bg-[#080E20]/95 backdrop-blur-xl border-t border-amber-500/30 z-30 flex items-center gap-2 shadow-2xl">
          <div className="flex-1 min-w-0 pl-1">
            <span className="text-[10px] text-amber-400 font-bold block uppercase tracking-wider">
              Menunggu Pembayaran
            </span>
            <span className="font-mono text-xs font-black text-[#CBAC70] truncate block">
              {order.pricing.formatted_grand_total}
            </span>
          </div>
          <button
            type="button"
            onClick={() =>
              order.payment?.payment_url
                ? (window.location.href = order.payment.payment_url)
                : onPayNow(order.orderNumber)
            }
            disabled={isGeneratingInvoice}
            className="py-2 px-4 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-bold text-xs shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center gap-1.5 active:scale-95 transition-all shrink-0 cursor-pointer"
          >
            <span>{isGeneratingInvoice ? 'Memuat...' : 'Bayar Sekarang'}</span>
            <ExternalLink className="w-3.5 h-3.5" />
          </button>
        </div>
      )}

      {/* 2. Modern Floating CS WhatsApp Button (FAB - Floating Action Button) */}
      {/* Floats elegantly above the bottom navigation bar on mobile (bottom-20 right-4) */}
      <a
        href={`https://wa.me/6281234567890?text=${waText}`}
        target="_blank"
        rel="noopener noreferrer"
        className={`fixed ${
          isUnpaid ? 'bottom-32' : 'bottom-20'
        } right-4 sm:bottom-6 sm:right-6 z-40 flex items-center gap-2 px-3.5 py-2.5 rounded-full bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-bold text-xs shadow-[0_4px_25px_rgba(16,185,129,0.45)] border border-emerald-300/30 active:scale-95 hover:scale-105 transition-all duration-300 backdrop-blur-md group select-none`}
        aria-label="Hubungi Bantuan CS WhatsApp"
        title="Hubungi Bantuan CS WhatsApp"
      >
        <div className="relative flex items-center justify-center">
          <MessageSquare className="w-4 h-4 text-white group-hover:rotate-6 transition-transform" />
          <span className="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-emerald-200 animate-ping" />
          <span className="absolute -top-1 -right-1 w-2 h-2 rounded-full bg-emerald-300" />
        </div>
        <span className="text-[11px] tracking-wide font-bold drop-shadow-sm">
          Bantuan CS
        </span>
      </a>
    </>
  );
}
