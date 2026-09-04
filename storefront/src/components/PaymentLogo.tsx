'use client';

import React, { useState } from 'react';

interface PaymentLogoProps {
  code?: string;
  category?: 'qris' | 'va' | 'card' | 'paylater' | 'cod';
  imageUrl?: string;
  name?: string;
  className?: string;
}

export default function PaymentLogo({
  code = '',
  category = 'va',
  imageUrl,
  name = '',
  className = ''
}: PaymentLogoProps) {
  const [imageError, setImageError] = useState(false);
  const cleanCode = (code || '').toUpperCase();

  // If image URL is provided from Duitku and has not errored
  if (imageUrl && !imageError) {
    return (
      <div className={`w-12 h-8 rounded-lg bg-white p-1 flex items-center justify-center shrink-0 border border-white/20 shadow-sm overflow-hidden ${className}`}>
        <img
          src={imageUrl}
          alt={name || code}
          onError={() => setImageError(true)}
          className="max-w-full max-h-full object-contain"
          loading="lazy"
        />
      </div>
    );
  }

  // Vector SVG Fallback Brand Badges for Top Indonesian Payment Channels
  switch (cleanCode) {
    case 'SP':
    case 'QR':
    case 'NQ':
    case 'LQ':
    case 'GQ':
      return (
        <div className={`w-12 h-8 rounded-lg bg-white flex flex-col items-center justify-center shrink-0 p-0.5 border border-red-500/30 shadow-sm ${className}`}>
          <div className="flex items-center gap-0.5">
            <span className="font-black text-[9px] tracking-tight text-[#E1251B] leading-none">QRIS</span>
          </div>
          <span className="text-[6px] font-bold text-gray-500 uppercase tracking-tighter leading-none">National</span>
        </div>
      );

    case 'BC':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#00529C] flex items-center justify-center shrink-0 px-1 border border-[#0060B2] shadow-sm ${className}`}>
          <span className="font-black text-xs tracking-wider text-white font-sans">BCA</span>
        </div>
      );

    case 'M2':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#002D62] flex flex-col items-center justify-center shrink-0 px-1 border border-amber-400/40 shadow-sm ${className}`}>
          <span className="font-black text-[10px] text-white tracking-tight leading-none">mandırı</span>
          <span className="w-5 h-0.5 bg-[#F59E0B] rounded-full mt-0.5"></span>
        </div>
      );

    case 'I1':
      return (
        <div className={`w-12 h-8 rounded-lg bg-white flex items-center justify-center shrink-0 px-1 border border-[#005E6A]/30 shadow-sm ${className}`}>
          <span className="font-black text-xs text-[#005E6A] tracking-tight font-sans">BNI</span>
          <span className="w-1.5 h-1.5 rounded-full bg-[#F15A24] ml-0.5"></span>
        </div>
      );

    case 'BR':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#00529C] flex items-center justify-center shrink-0 px-1 border border-white/20 shadow-sm ${className}`}>
          <span className="font-black text-xs text-white tracking-tight font-sans">BRI</span>
        </div>
      );

    case 'BT':
      return (
        <div className={`w-12 h-8 rounded-lg bg-white flex items-center justify-center shrink-0 px-1 border border-emerald-500/30 shadow-sm ${className}`}>
          <span className="font-black text-[9px] text-[#008852] tracking-tighter uppercase font-sans">Permata</span>
        </div>
      );

    case 'B1':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#7A0019] flex items-center justify-center shrink-0 px-1 border border-red-600/40 shadow-sm ${className}`}>
          <span className="font-black text-[8px] text-white tracking-tighter uppercase font-sans">CIMB</span>
        </div>
      );

    case 'VC':
      return (
        <div className={`w-12 h-8 rounded-lg bg-white flex items-center justify-center shrink-0 gap-0.5 px-0.5 border border-white/20 shadow-sm ${className}`}>
          <span className="font-black text-[9px] text-[#1A1F71] italic leading-none font-serif">VISA</span>
          <div className="flex -space-x-1 items-center">
            <span className="w-2.5 h-2.5 rounded-full bg-[#EB001B] opacity-90 inline-block"></span>
            <span className="w-2.5 h-2.5 rounded-full bg-[#F79E1B] opacity-90 inline-block"></span>
          </div>
        </div>
      );

    case 'DA':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#118EEA] flex items-center justify-center shrink-0 px-1 border border-blue-400/30 shadow-sm ${className}`}>
          <span className="font-black text-[10px] text-white tracking-tight font-sans">DANA</span>
        </div>
      );

    case 'OV':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#4C2A86] flex items-center justify-center shrink-0 px-1 border border-purple-500/40 shadow-sm ${className}`}>
          <span className="font-black text-[10px] text-white tracking-widest font-sans">OVO</span>
        </div>
      );

    case 'SA':
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#EE4D2D] flex items-center justify-center shrink-0 px-1 border border-orange-500/40 shadow-sm ${className}`}>
          <span className="font-black text-[8px] text-white tracking-tighter font-sans">Shopee</span>
        </div>
      );

    case 'COD':
      return (
        <div className={`w-12 h-8 rounded-lg bg-gradient-to-br from-[#E3CD99] via-[#CBAC70] to-[#A58645] flex flex-col items-center justify-center shrink-0 px-1 shadow-sm ${className}`}>
          <span className="font-black text-[10px] text-[#0B132B] tracking-wider leading-none">COD</span>
          <span className="text-[6px] font-bold text-[#0B132B] uppercase tracking-tighter leading-none mt-0.5">Cash</span>
        </div>
      );

    default:
      if (category === 'qris') {
        return (
          <div className={`w-12 h-8 rounded-lg bg-white flex flex-col items-center justify-center shrink-0 p-0.5 border border-red-500/30 shadow-sm ${className}`}>
            <span className="font-black text-[9px] tracking-tight text-[#E1251B] leading-none">QRIS</span>
          </div>
        );
      }
      if (category === 'card') {
        return (
          <div className={`w-12 h-8 rounded-lg bg-white flex items-center justify-center shrink-0 px-1 border border-white/20 shadow-sm ${className}`}>
            <span className="font-black text-[9px] text-[#1A1F71] italic">CARD</span>
          </div>
        );
      }
      return (
        <div className={`w-12 h-8 rounded-lg bg-[#14204A] border border-[#CBAC70]/40 flex items-center justify-center shrink-0 px-1 shadow-sm ${className}`}>
          <span className="font-mono font-bold text-[9px] text-[#CBAC70]">{cleanCode || 'BANK'}</span>
        </div>
      );
  }
}
