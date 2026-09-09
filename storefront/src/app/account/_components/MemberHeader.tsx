'use client';

import React from 'react';
import { LogOut, ShieldCheck, Phone } from 'lucide-react';
import { CustomerProfile } from '../../../types';

interface MemberHeaderProps {
  customer: CustomerProfile | null;
  logout: () => void;
  onEditProfile?: () => void;
}

export default function MemberHeader({ customer, logout, onEditProfile }: MemberHeaderProps) {
  return (
    <div className="rounded-3xl bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] p-5 sm:p-7 border border-[#CBAC70]/30 shadow-2xl flex flex-col md:flex-row md:items-center justify-between gap-5">
      <div className="flex items-center gap-4 min-w-0">
        <div className="w-14 h-14 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-lg sm:text-xl shadow-lg shrink-0">
          {customer?.name.substring(0, 2).toUpperCase()}
        </div>
        <div className="space-y-1.5 min-w-0">
          <div className="flex items-center gap-2 flex-wrap">
            <h1 className="text-lg sm:text-2xl font-black text-white truncate max-w-xs sm:max-w-md">
              {customer?.name}
            </h1>
            <span
              className={`px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold shrink-0 ${
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

          <div className="text-xs text-slate-400 font-mono flex items-center gap-2.5 flex-wrap">
            <span className="flex items-center gap-1 text-slate-300 truncate">
              <span>✉</span>
              <span>{customer?.email}</span>
            </span>

            <span>•</span>

            {customer?.phone ? (
              <span className="flex items-center gap-1 text-emerald-400 font-semibold">
                <Phone className="w-3 h-3 text-emerald-400" />
                <span>{customer.phone}</span>
              </span>
            ) : (
              <button
                type="button"
                onClick={onEditProfile}
                className="text-amber-400 hover:text-amber-300 font-bold underline underline-offset-2 flex items-center gap-1 cursor-pointer transition-colors text-[11px]"
                title="Atur nomor WhatsApp untuk notifikasi resi ekspedisi"
              >
                <span>📱 + Atur No. WhatsApp</span>
              </button>
            )}
          </div>
        </div>
      </div>

      {/* Member Stats & Logout */}
      <div className="flex items-center gap-3 sm:gap-4 self-end md:self-auto shrink-0">
        <div className="text-right px-4 py-2 rounded-2xl bg-black/40 border border-white/5">
          <p className="text-[10px] font-mono uppercase text-[#CBAC70] tracking-wider">
            Akumulasi Belanja
          </p>
          <p className="text-sm sm:text-base font-black font-mono text-white">
            {customer?.formatted_spend ||
              `Rp ${(customer?.total_spend || 0).toLocaleString('id-ID')}`}
          </p>
        </div>

        <button
          type="button"
          onClick={logout}
          className="p-3 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all active:scale-95 cursor-pointer shadow-md"
          title="Keluar (Logout)"
          aria-label="Keluar dari akun"
        >
          <LogOut className="w-4 h-4 sm:w-5 sm:h-5" />
        </button>
      </div>
    </div>
  );
}
