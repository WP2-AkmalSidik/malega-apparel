'use client';

import React, { useState, useEffect } from 'react';
import {
  LogOut,
  ShieldCheck,
  Phone,
  Crown,
  Sparkles,
  TrendingUp,
  Info,
  ChevronRight,
  Gift,
  ShoppingBag,
} from 'lucide-react';
import { CustomerProfile } from '../../../types';
import TierBenefitsModal from './TierBenefitsModal';

interface MemberHeaderProps {
  customer: CustomerProfile | null;
  logout: () => void;
  onEditProfile?: () => void;
}

interface BasicTier {
  name: string;
  min_spend: number;
  discount_label?: string;
}

export default function MemberHeader({
  customer,
  logout,
  onEditProfile,
}: MemberHeaderProps) {
  const [showTierModal, setShowTierModal] = useState(false);
  const [tiers, setTiers] = useState<BasicTier[]>([]);

  const API_BASE =
    process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  useEffect(() => {
    fetch(`${API_BASE}/membership-tiers`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success && Array.isArray(data.data) && data.data.length > 0) {
          setTiers(data.data);
        }
      })
      .catch(() => {});
  }, [API_BASE]);

  const spend = customer?.total_spend || 0;
  const currentTier = customer?.membership_tier || 'Silver';

  // Calculate progress towards next tier based on Malega tier thresholds
  let nextTierName = 'Gold';
  let remainingSpend = Math.max(0, 500000 - spend);
  let progressPercent = Math.min(100, Math.round((spend / 500000) * 100));
  let nextDiscountLabel = 'Diskon 15% & Bebas Ongkir';

  if (tiers.length > 0) {
    const idx = tiers.findIndex(
      (t) => t.name.toLowerCase() === currentTier.toLowerCase()
    );
    if (idx !== -1 && idx < tiers.length - 1) {
      const nextT = tiers[idx + 1];
      nextTierName = nextT.name;
      remainingSpend = Math.max(0, nextT.min_spend - spend);
      const prevMin = tiers[idx].min_spend;
      const range = nextT.min_spend - prevMin;
      progressPercent =
        range > 0
          ? Math.min(
              100,
              Math.max(0, Math.round(((spend - prevMin) / range) * 100))
            )
          : 100;
      nextDiscountLabel = nextT.discount_label || 'Benefit Eksklusif';
    } else if (idx === tiers.length - 1) {
      nextTierName = 'Tingkat Tertinggi';
      remainingSpend = 0;
      progressPercent = 100;
    }
  } else {
    // Fallback if API hasn't resolved
    if (currentTier === 'Gold') {
      nextTierName = 'VIP Platinum';
      remainingSpend = Math.max(0, 1500000 - spend);
      progressPercent = Math.min(
        100,
        Math.round(((spend - 500000) / 1000000) * 100)
      );
      nextDiscountLabel = 'Diskon 20% & Early Drop 24 Jam';
    } else if (currentTier === 'VIP Platinum') {
      nextTierName = 'Tingkat Tertinggi';
      remainingSpend = 0;
      progressPercent = 100;
    }
  }

  // Tier styling helpers
  const getTierBadgeStyle = () => {
    switch (currentTier) {
      case 'VIP Platinum':
        return 'bg-[#CBAC70]/20 text-[#CBAC70] border-[#CBAC70]/60 hover:bg-[#CBAC70]/30 shadow-[0_0_15px_rgba(203,172,112,0.2)]';
      case 'Gold':
        return 'bg-amber-500/20 text-amber-300 border-amber-500/50 hover:bg-amber-500/30';
      default:
        return 'bg-slate-800/90 text-slate-200 border-slate-700 hover:border-slate-500 hover:bg-slate-700/80';
    }
  };

  const getTierIcon = () => {
    switch (currentTier) {
      case 'VIP Platinum':
        return Crown;
      case 'Gold':
        return Sparkles;
      default:
        return ShieldCheck;
    }
  };

  const TierIcon = getTierIcon();

  return (
    <>
      <div className="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#14204A] via-[#0E1736] to-[#070D1F] p-4 sm:p-7 border border-[#CBAC70]/30 shadow-2xl space-y-5">
        {/* Luxury Background Glows & Watermark */}
        <div className="pointer-events-none absolute -right-16 -top-16 w-64 h-64 bg-[#CBAC70]/10 rounded-full blur-3xl" />
        <div className="pointer-events-none absolute -left-16 -bottom-16 w-64 h-64 bg-[#14204A]/50 rounded-full blur-3xl" />
        <div className="pointer-events-none absolute top-4 right-8 select-none text-[80px] font-black text-white/[0.02] tracking-widest hidden md:block">
          MALEGA
        </div>

        {/* Top Row: Avatar, Identity, Interactive Tier Badge, & Logout */}
        <div className="relative z-10 flex items-start justify-between gap-4">
          <div className="flex items-center gap-3 sm:gap-4 min-w-0">
            {/* Avatar Initial */}
            <div className="w-13 h-13 sm:w-16 sm:h-16 rounded-2xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] font-black text-base sm:text-xl shadow-lg shrink-0 border border-white/20">
              {customer?.name ? customer.name.substring(0, 2).toUpperCase() : 'MA'}
            </div>

            {/* Customer Details & Interactive Badge */}
            <div className="space-y-1 min-w-0">
              <div className="flex items-center gap-2 flex-wrap">
                <h1 className="text-base sm:text-2xl font-black text-white truncate max-w-[200px] sm:max-w-md">
                  {customer?.name}
                </h1>

                {/* Clickable Tier Badge */}
                <button
                  type="button"
                  onClick={() => setShowTierModal(true)}
                  className={`group inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-mono font-bold border transition-all cursor-pointer active:scale-95 ${getTierBadgeStyle()}`}
                  title="Klik untuk melihat aturan & keuntungan tier member"
                >
                  <TierIcon className="w-3 h-3 text-[#CBAC70] group-hover:rotate-12 transition-transform" />
                  <span>★ {currentTier} Member</span>
                  <Info className="w-2.5 h-2.5 opacity-60 group-hover:opacity-100" />
                </button>
              </div>

              {/* Email & WhatsApp Row */}
              <div className="text-[11px] sm:text-xs text-slate-400 font-mono flex items-center gap-2 flex-wrap">
                <span className="flex items-center gap-1 text-slate-300 truncate">
                  <span>✉</span>
                  <span className="truncate max-w-[160px] sm:max-w-xs">
                    {customer?.email}
                  </span>
                </span>

                <span className="text-slate-600">•</span>

                {customer?.phone ? (
                  <span className="flex items-center gap-1 text-emerald-400 font-semibold">
                    <Phone className="w-3 h-3 text-emerald-400 shrink-0" />
                    <span>{customer.phone}</span>
                  </span>
                ) : (
                  <button
                    type="button"
                    onClick={onEditProfile}
                    className="text-amber-400 hover:text-amber-300 font-bold underline underline-offset-2 flex items-center gap-1 cursor-pointer transition-colors text-[10px] sm:text-[11px]"
                    title="Atur nomor WhatsApp untuk notifikasi resi pengiriman"
                  >
                    <span>📱 + Atur No. WhatsApp</span>
                  </button>
                )}
              </div>
            </div>
          </div>

          {/* Quick Logout Button */}
          <button
            type="button"
            onClick={logout}
            className="p-2.5 sm:p-3 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all active:scale-95 cursor-pointer shadow-md shrink-0"
            title="Keluar (Logout)"
            aria-label="Keluar dari akun"
          >
            <LogOut className="w-4 h-4 sm:w-4 sm:h-4" />
          </button>
        </div>

        {/* Middle Section: Gamified Tier Milestone Progress Bar */}
        <div className="relative z-10 rounded-2xl bg-black/40 border border-white/10 p-3.5 sm:p-4 space-y-2.5">
          <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-1.5 text-xs">
            <div className="flex items-center gap-2">
              <TrendingUp className="w-3.5 h-3.5 text-[#CBAC70]" />
              <span className="text-slate-300 font-medium">
                {currentTier === 'VIP Platinum' ? (
                  <span className="text-amber-300 font-bold">
                    👑 Member VIP Tingkat Tertinggi
                  </span>
                ) : (
                  <>
                    Target Level:{' '}
                    <strong className="text-white">{nextTierName} Member</strong>
                  </>
                )}
              </span>
            </div>

            <div className="flex items-center justify-between sm:justify-end gap-2">
              <span className="text-[11px] font-mono text-[#CBAC70] font-bold">
                {progressPercent}% Menuju {nextTierName}
              </span>
              <button
                type="button"
                onClick={() => setShowTierModal(true)}
                className="text-[11px] text-[#CBAC70] hover:text-[#E3CD99] font-mono underline underline-offset-2 flex items-center gap-0.5 cursor-pointer"
              >
                <span>Aturan Tier</span>
                <ChevronRight className="w-3 h-3" />
              </button>
            </div>
          </div>

          {/* Progress Bar Track */}
          <div className="h-2 sm:h-2.5 w-full bg-[#070D1F] rounded-full overflow-hidden border border-white/10 relative">
            <div
              className="h-full bg-gradient-to-r from-[#CBAC70] via-[#F3E5AB] to-[#CBAC70] rounded-full transition-all duration-700 shadow-[0_0_10px_rgba(203,172,112,0.5)]"
              style={{ width: `${Math.max(4, progressPercent)}%` }}
            />
          </div>

          {/* Motivational Milestone Prompt */}
          <div className="text-[11px] text-slate-400 font-mono flex items-center justify-between flex-wrap gap-2 pt-0.5">
            {currentTier !== 'VIP Platinum' ? (
              <span>
                💡 Belanja{' '}
                <strong className="text-amber-300">
                  Rp {remainingSpend.toLocaleString('id-ID')}
                </strong>{' '}
                lagi untuk unlock{' '}
                <span className="text-white font-semibold">
                  {nextDiscountLabel}
                </span>
                .
              </span>
            ) : (
              <span className="text-amber-300">
                ✨ Selamat! Anda berhak menikmati diskon 20% all-item & layanan concierge prioritas seumur hidup.
              </span>
            )}
          </div>
        </div>

        {/* Bottom Section: 3-Pillar Stat Ribbon */}
        <div className="relative z-10 grid grid-cols-3 gap-2 sm:gap-3">
          {/* Stat 1: Total Spend */}
          <div className="p-3 sm:p-3.5 rounded-2xl bg-[#0E1736]/90 border border-white/10 flex flex-col justify-between">
            <span className="text-[9px] sm:text-[10px] font-mono uppercase tracking-wider text-slate-400 block">
              Total Belanja
            </span>
            <div className="mt-1">
              <span className="text-xs sm:text-base font-black font-mono text-emerald-400 truncate block">
                {customer?.formatted_spend ||
                  `Rp ${(customer?.total_spend || 0).toLocaleString('id-ID')}`}
              </span>
              <span className="text-[9px] sm:text-[10px] text-slate-400 font-mono block mt-0.5">
                Akumulasi Belanja
              </span>
            </div>
          </div>

          {/* Stat 2: Total Orders */}
          <div className="p-3 sm:p-3.5 rounded-2xl bg-[#0E1736]/90 border border-white/10 flex flex-col justify-between">
            <span className="text-[9px] sm:text-[10px] font-mono uppercase tracking-wider text-slate-400 block flex items-center gap-1">
              <ShoppingBag className="w-3 h-3 text-[#CBAC70] shrink-0 hidden sm:inline-block" />
              <span>Pesanan Selesai</span>
            </span>
            <div className="mt-1">
              <span className="text-xs sm:text-base font-black font-mono text-white block">
                {customer?.total_orders || 0} Transaksi
              </span>
              <span className="text-[9px] sm:text-[10px] text-slate-400 font-mono block mt-0.5">
                Riwayat Berhasil
              </span>
            </div>
          </div>

          {/* Stat 3: Tier Benefit & Interactive Modal Trigger */}
          <button
            type="button"
            onClick={() => setShowTierModal(true)}
            className="p-3 sm:p-3.5 rounded-2xl bg-gradient-to-br from-[#121B3B] to-[#0A1024] border border-[#CBAC70]/40 flex flex-col justify-between text-left hover:border-[#CBAC70] transition-all group active:scale-95 cursor-pointer shadow-md"
            title="Klik untuk membuka rincian benefit & kupon"
          >
            <div className="flex items-center justify-between w-full">
              <span className="text-[9px] sm:text-[10px] font-mono uppercase tracking-wider text-[#CBAC70] font-bold">
                Keuntungan Tier
              </span>
              <ChevronRight className="w-3 h-3 text-[#CBAC70] group-hover:translate-x-0.5 transition-transform" />
            </div>
            <div className="mt-1">
              <span className="text-xs sm:text-base font-black font-mono text-white block">
                {currentTier === 'VIP Platinum'
                  ? 'Diskon 20%'
                  : currentTier === 'Gold'
                  ? 'Diskon 15%'
                  : 'Diskon 10%'}
              </span>
              <span className="text-[9px] sm:text-[10px] text-[#CBAC70] font-mono block mt-0.5 underline underline-offset-1">
                Lihat Benefit →
              </span>
            </div>
          </button>
        </div>
      </div>

      {/* Tier Benefits & Rules Modal */}
      <TierBenefitsModal
        isOpen={showTierModal}
        onClose={() => setShowTierModal(false)}
        customer={customer}
      />
    </>
  );
}
