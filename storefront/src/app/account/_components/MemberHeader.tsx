'use client';

import React from 'react';
import { LogOut } from 'lucide-react';
import { CustomerProfile } from '../../../types';

interface MemberHeaderProps {
  customer: CustomerProfile | null;
  logout: () => void;
}

export default function MemberHeader({ customer, logout }: MemberHeaderProps) {
  return (
    <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-6 border border-[#CBAC70]/30 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div className="flex items-center gap-4">
        <div className="w-16 h-16 rounded-2xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-xl shadow-lg shrink-0">
          {customer?.name.substring(0, 2).toUpperCase()}
        </div>
        <div className="space-y-1">
          <div className="flex items-center gap-2 flex-wrap">
            <h1 className="text-xl sm:text-2xl font-black text-white">{customer?.name}</h1>
            <span
              className={`px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold ${
                customer?.membership_tier === 'VIP Platinum'
                  ? 'bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/50'
                  : customer?.membership_tier === 'Gold'
                  ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                  : 'bg-slate-800 text-slate-300 border border-slate-700'
              }`}
            >
              ★ {customer?.membership_tier} Member
            </span>
          </div>
          <p className="text-xs text-slate-400 font-mono flex items-center gap-3">
            <span>✉ {customer?.email}</span>
            <span>•</span>
            <span>📱 {customer?.phone}</span>
          </p>
        </div>
      </div>

      {/* Member Stats & Logout */}
      <div className="flex items-center gap-4 self-end md:self-auto">
        <div className="text-right px-4 py-2 rounded-2xl bg-black/40 border border-white/5">
          <p className="text-[10px] font-mono uppercase text-[#CBAC70]">Akumulasi Belanja</p>
          <p className="text-sm sm:text-base font-black font-mono text-white">
            {customer?.formatted_spend ||
              `Rp ${(customer?.total_spend || 0).toLocaleString('id-ID')}`}
          </p>
        </div>

        <button
          type="button"
          onClick={logout}
          className="p-3 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition cursor-pointer"
          title="Keluar (Logout)"
        >
          <LogOut className="w-5 h-5" />
        </button>
      </div>
    </div>
  );
}
