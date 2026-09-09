'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import {
  X,
  Crown,
  Sparkles,
  Check,
  Copy,
  ArrowRight,
  ShieldCheck,
  Gift,
  Truck,
  Zap,
  HelpCircle,
  Clock,
  TrendingUp,
} from 'lucide-react';
import { CustomerProfile } from '../../../types';

interface DynamicTier {
  id: number;
  name: string;
  slug: string;
  min_spend: number;
  formatted_min_spend: string;
  badge_text: string;
  badge_color: string;
  discount_label?: string;
  description?: string;
  perks: Array<{ icon?: string; title: string; desc?: string }>;
  voucher?: {
    id: number;
    code: string;
    name: string;
    type: string;
    amount: number;
    formatted_discount: string;
  } | null;
}

interface TierBenefitsModalProps {
  isOpen: boolean;
  onClose: () => void;
  customer: CustomerProfile | null;
}

const DEFAULT_TIERS = [
  {
    id: 1,
    name: 'Silver',
    slug: 'silver',
    min_spend: 0,
    formatted_min_spend: 'Rp 0',
    badge_text: '★ Silver Member',
    badge_color: 'slate',
    discount_label: 'Diskon 10%',
    description: 'Tingkat keanggotaan awal untuk seluruh pelanggan terdaftar Malega Apparel.',
    voucher: {
      id: 4,
      code: 'WELCOME10',
      name: 'Welcome Atelier 10% OFF',
      type: 'percentage',
      amount: 10,
      formatted_discount: 'Diskon 10%',
    },
    perks: [
      { icon: 'gift', title: 'Voucher Sambutan 10%', desc: 'Gunakan kode WELCOME10 untuk potongan 10% pembelian pertama.' },
      { icon: 'zap', title: 'Realtime Live Tracking', desc: 'Pantau status pesanan dan pergerakan resi ekspedisi langsung di web.' },
      { icon: 'sparkles', title: 'Akses Rilis Resmi', desc: 'Dapatkan pemberitahuan pertama saat katalog baru diluncurkan.' },
    ],
  },
  {
    id: 2,
    name: 'Gold',
    slug: 'gold',
    min_spend: 500000,
    formatted_min_spend: 'Rp 500.000',
    badge_text: '★ Gold Member',
    badge_color: 'amber',
    discount_label: 'Diskon 15%',
    description: 'Tingkat member reguler bagi pelanggan yang telah berbelanja minimal Rp 500.000.',
    voucher: {
      id: 1,
      code: 'MALEGAVIP15',
      name: 'VIP Gold Member 15% OFF',
      type: 'percentage',
      amount: 15,
      formatted_discount: 'Diskon 15%',
    },
    perks: [
      { icon: 'gift', title: 'Diskon 15% New Drop', desc: 'Kupon MALEGAVIP15 untuk diskon 15% setiap peluncuran artikel baru.' },
      { icon: 'truck', title: 'Bebas / Subsidi Ongkir', desc: 'Gratis atau potongan ongkos kirim reguler ke seluruh kota di Indonesia.' },
      { icon: 'zap', title: 'Priority Dispatch Packing', desc: 'Paket diprioritaskan untuk proses QC dan pengiriman oleh tim gudang.' },
    ],
  },
  {
    id: 3,
    name: 'VIP Platinum',
    slug: 'vip-platinum',
    min_spend: 1500000,
    formatted_min_spend: 'Rp 1.500.000',
    badge_text: '👑 VIP Platinum Member',
    badge_color: 'gold',
    discount_label: 'Diskon 20%',
    description: 'Tingkat eksklusif tertinggi bagi pelanggan loyal dengan akumulasi belanja minimal Rp 1.500.000.',
    voucher: {
      id: 5,
      code: 'MEMBERONLY20',
      name: 'Khusus Member Terdaftar 20%',
      type: 'percentage',
      amount: 20,
      formatted_discount: 'Diskon 20%',
    },
    perks: [
      { icon: 'crown', title: 'Diskon 20% Seumur Hidup', desc: 'Kupon MEMBERONLY20 untuk potongan 20% all-item tanpa batas kuota.' },
      { icon: 'clock', title: 'Early-Bird Drop 24 Jam', desc: 'Beli koleksi limited edition 24 jam lebih awal sebelum dirilis ke publik.' },
      { icon: 'sparkles', title: 'VIP WhatsApp Concierge', desc: 'Akses nomor WhatsApp personal assist khusus konsultasi size & fast-track order.' },
    ],
  },
];

export default function TierBenefitsModal({
  isOpen,
  onClose,
  customer,
}: TierBenefitsModalProps) {
  const [copiedCode, setCopiedCode] = useState<string | null>(null);
  const [tiers, setTiers] = useState<DynamicTier[]>(DEFAULT_TIERS);

  const API_BASE =
    process.env.NEXT_PUBLIC_BACKEND_API_URL || 'https://malega.my.id/api/v1';

  // Fetch dynamic master tiers from backend API
  useEffect(() => {
    if (!isOpen) return;
    fetch(`${API_BASE}/membership-tiers`)
      .then((res) => res.json())
      .then((data) => {
        if (data.success && Array.isArray(data.data) && data.data.length > 0) {
          setTiers(data.data);
        }
      })
      .catch((err) => {
        console.error('Failed to load dynamic membership tiers:', err);
      });
  }, [isOpen, API_BASE]);

  if (!isOpen) return null;

  const spend = customer?.total_spend || 0;
  const currentTierName = customer?.membership_tier || 'Silver';

  // Copy voucher code helper
  const handleCopyCode = (code: string) => {
    navigator.clipboard.writeText(code);
    setCopiedCode(code);
    setTimeout(() => setCopiedCode(null), 2000);
  };

  // Find next tier from dynamic tiers
  const currentTierIndex = tiers.findIndex(
    (t) => t.name.toLowerCase() === currentTierName.toLowerCase()
  );

  let nextTier: DynamicTier | null = null;
  let remainingSpend = 0;
  let progressPercent = 100;

  if (currentTierIndex !== -1 && currentTierIndex < tiers.length - 1) {
    nextTier = tiers[currentTierIndex + 1];
    remainingSpend = Math.max(0, nextTier.min_spend - spend);
    const prevMin = tiers[currentTierIndex].min_spend;
    const range = nextTier.min_spend - prevMin;
    progressPercent =
      range > 0
        ? Math.min(100, Math.max(0, Math.round(((spend - prevMin) / range) * 100)))
        : 100;
  } else if (currentTierIndex === -1 && tiers.length > 1) {
    // If not found, target is second tier
    nextTier = tiers[1];
    remainingSpend = Math.max(0, nextTier.min_spend - spend);
    progressPercent = Math.min(100, Math.round((spend / nextTier.min_spend) * 100));
  }

  const getPerkIcon = (iconName?: string) => {
    switch (iconName) {
      case 'zap':
        return Zap;
      case 'truck':
        return Truck;
      case 'crown':
        return Crown;
      case 'sparkles':
        return Sparkles;
      case 'clock':
        return Clock;
      default:
        return Gift;
    }
  };

  const getTierIcon = (color?: string, name?: string) => {
    if (color === 'gold' || name?.toLowerCase().includes('platinum')) return Crown;
    if (color === 'amber' || name?.toLowerCase().includes('gold')) return Sparkles;
    return ShieldCheck;
  };

  const getTierBadgeStyle = (color?: string) => {
    switch (color) {
      case 'gold':
        return 'bg-[#CBAC70]/20 text-[#CBAC70] border-[#CBAC70]/50';
      case 'amber':
        return 'bg-amber-500/20 text-amber-300 border-amber-500/40';
      case 'emerald':
        return 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
      case 'rose':
        return 'bg-rose-500/20 text-rose-300 border-rose-500/40';
      default:
        return 'bg-slate-800 text-slate-300 border-slate-700';
    }
  };

  const getBorderActiveStyle = (color?: string) => {
    switch (color) {
      case 'gold':
        return 'border-[#CBAC70] ring-2 ring-[#CBAC70]/50 shadow-[0_0_25px_rgba(203,172,112,0.2)]';
      case 'amber':
        return 'border-amber-400 ring-2 ring-amber-400/40';
      default:
        return 'border-slate-400 ring-2 ring-slate-400/30';
    }
  };

  return (
    <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-md flex items-center justify-center p-3 sm:p-4 overflow-y-auto animate-in fade-in duration-200">
      <div className="bg-[#0B132B] border border-[#CBAC70]/40 rounded-3xl max-w-2xl w-full my-auto shadow-2xl overflow-hidden flex flex-col text-[#FDFCFF] max-h-[92vh]">
        {/* Header */}
        <div className="p-5 sm:p-6 border-b border-white/10 flex items-center justify-between bg-gradient-to-r from-[#14204A] via-[#0E1736] to-[#0A1024] shrink-0">
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 rounded-2xl bg-gradient-to-br from-[#CBAC70] to-[#997732] flex items-center justify-center text-[#0B132B] shadow-lg">
              <Crown className="w-5 h-5" />
            </div>
            <div>
              <h2 className="text-base sm:text-lg font-black text-white tracking-wide flex items-center gap-2">
                <span>Aturan & Privilege Member</span>
                <span className="text-[10px] font-mono px-2 py-0.5 rounded-full bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/40 hidden sm:inline-block">
                  MALEGA CLUB
                </span>
              </h2>
              <p className="text-xs text-slate-400">
                Sistem loyalitas otomatis tanpa batas kedaluwarsa
              </p>
            </div>
          </div>

          <button
            type="button"
            onClick={onClose}
            className="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-white/10 transition active:scale-95 cursor-pointer"
            aria-label="Tutup modal"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Scrollable Content */}
        <div className="p-4 sm:p-6 space-y-6 overflow-y-auto custom-scrollbar">
          {/* Active User Tier Status Summary */}
          <div className="rounded-2xl bg-gradient-to-br from-[#121B3B] to-[#0A1024] p-4 sm:p-5 border border-[#CBAC70]/30 relative overflow-hidden shadow-inner">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-3 relative z-10">
              <div>
                <span className="text-[10px] font-mono uppercase tracking-wider text-[#CBAC70] font-semibold">
                  Status Akun Anda
                </span>
                <div className="flex items-center gap-2 mt-0.5">
                  <h3 className="text-lg font-black text-white">
                    {customer?.name}
                  </h3>
                  <span
                    className={`px-2.5 py-0.5 rounded-full text-xs font-mono font-bold border ${getTierBadgeStyle(
                      tiers.find((t) => t.name === currentTierName)?.badge_color
                    )}`}
                  >
                    ★ {currentTierName} Member
                  </span>
                </div>
              </div>

              <div className="sm:text-right bg-black/40 px-3.5 py-2 rounded-xl border border-white/5">
                <span className="text-[10px] font-mono uppercase text-slate-400 block">
                  Total Belanja Terakumulasi
                </span>
                <span className="text-sm sm:text-base font-black font-mono text-emerald-400">
                  {customer?.formatted_spend ||
                    `Rp ${spend.toLocaleString('id-ID')}`}
                </span>
              </div>
            </div>

            {/* Progress to Next Tier */}
            {nextTier ? (
              <div className="mt-4 pt-3 border-t border-white/10 space-y-2">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-slate-300 font-medium flex items-center gap-1.5">
                    <TrendingUp className="w-3.5 h-3.5 text-[#CBAC70]" />
                    <span>
                      Target: <strong>{nextTier.name} Member</strong>
                    </span>
                  </span>
                  <span className="font-mono text-[#CBAC70] font-bold">
                    {progressPercent}%
                  </span>
                </div>

                {/* Progress Bar */}
                <div className="h-2 w-full bg-slate-900 rounded-full overflow-hidden border border-white/10">
                  <div
                    className="h-full bg-gradient-to-r from-[#CBAC70] via-[#E3CD99] to-[#CBAC70] rounded-full transition-all duration-500"
                    style={{ width: `${Math.max(5, progressPercent)}%` }}
                  />
                </div>

                <p className="text-[11px] text-slate-400 font-mono">
                  💡 Belanja{' '}
                  <span className="text-amber-300 font-bold">
                    Rp {remainingSpend.toLocaleString('id-ID')}
                  </span>{' '}
                  lagi untuk membuka level {nextTier.name}{' '}
                  {nextTier.discount_label ? `dan ${nextTier.discount_label}` : ''}.
                </p>
              </div>
            ) : (
              <div className="mt-3 pt-3 border-t border-white/10 flex items-center gap-2 text-xs text-amber-300 font-medium">
                <Crown className="w-4 h-4 text-[#CBAC70] shrink-0" />
                <span>Selamat! Anda berada di tingkat keanggotaan tertinggi Malega Privilege Club.</span>
              </div>
            )}
          </div>

          {/* Rule Explanations / How it Works */}
          <div className="space-y-2">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold flex items-center gap-1.5">
              <HelpCircle className="w-3.5 h-3.5 text-[#CBAC70]" />
              <span>Bagaimana Cara Kerja Tingkatan Member?</span>
            </h4>
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 text-xs">
              <div className="p-3 rounded-xl bg-[#0E1736] border border-white/5 space-y-1">
                <div className="text-amber-400 font-bold font-mono">1. Akumulasi Otomatis</div>
                <p className="text-slate-400 text-[11px] leading-relaxed">
                  Total belanja bertambah otomatis setiap transaksi pesanan Anda selesai dan sukses diterima.
                </p>
              </div>
              <div className="p-3 rounded-xl bg-[#0E1736] border border-white/5 space-y-1">
                <div className="text-amber-400 font-bold font-mono">2. Seumur Hidup</div>
                <p className="text-slate-400 text-[11px] leading-relaxed">
                  Tier Anda tidak akan pernah turun atau kedaluwarsa. Sekali naik level, benefit aktif selamanya.
                </p>
              </div>
              <div className="p-3 rounded-xl bg-[#0E1736] border border-white/5 space-y-1">
                <div className="text-amber-400 font-bold font-mono">3. Eksklusif & Instan</div>
                <p className="text-slate-400 text-[11px] leading-relaxed">
                  Kupon khusus tier terkunci eksklusif dan dapat langsung digunakan saat checkout.
                </p>
              </div>
            </div>
          </div>

          {/* Tier Cards List */}
          <div className="space-y-3">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold">
              Daftar Tingkatan & Kupon Eksklusif
            </h4>

            {tiers.map((tier) => {
              const isCurrent =
                currentTierName.toLowerCase() === tier.name.toLowerCase();
              const TierIcon = getTierIcon(tier.badge_color, tier.name);

              return (
                <div
                  key={tier.id}
                  className={`rounded-2xl p-4 sm:p-5 border transition-all ${
                    isCurrent
                      ? `bg-[#0E1736] ${getBorderActiveStyle(tier.badge_color)}`
                      : 'bg-[#0A1024]/60 border-white/10 hover:border-white/20'
                  }`}
                >
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-white/10">
                    <div className="flex items-center gap-3">
                      <div
                        className={`w-9 h-9 rounded-xl flex items-center justify-center font-bold shrink-0 ${
                          tier.badge_color === 'gold'
                            ? 'bg-[#CBAC70] text-[#0B132B]'
                            : tier.badge_color === 'amber'
                            ? 'bg-amber-500 text-slate-950'
                            : 'bg-slate-700 text-slate-200'
                        }`}
                      >
                        <TierIcon className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2 flex-wrap">
                          <h5 className="font-bold text-white text-sm">
                            {tier.badge_text || `${tier.name} Member`}
                          </h5>
                          {isCurrent && (
                            <span className="px-2 py-0.5 rounded-full text-[10px] font-mono font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 animate-pulse">
                              ● Tier Anda Saat Ini
                            </span>
                          )}
                        </div>
                        <p className="text-[11px] font-mono text-slate-400">
                          Syarat: Min. Belanja {tier.formatted_min_spend || `Rp ${tier.min_spend.toLocaleString('id-ID')}`}
                        </p>
                      </div>
                    </div>

                    {/* Voucher Pill (Khusus ditempelkan ke tier ini) */}
                    {tier.voucher ? (
                      <div className="flex items-center gap-1.5 self-start sm:self-auto bg-black/40 px-3 py-1.5 rounded-xl border border-white/10">
                        <span className="text-[10px] font-mono text-slate-400">Kupon:</span>
                        <code className="text-xs font-mono font-black text-[#CBAC70]">
                          {tier.voucher.code}
                        </code>
                        <button
                          type="button"
                          onClick={() => handleCopyCode(tier.voucher!.code)}
                          className="text-slate-400 hover:text-white p-0.5 transition cursor-pointer"
                          title="Salin kode kupon"
                        >
                          {copiedCode === tier.voucher.code ? (
                            <Check className="w-3.5 h-3.5 text-emerald-400" />
                          ) : (
                            <Copy className="w-3.5 h-3.5" />
                          )}
                        </button>
                      </div>
                    ) : (
                      <span className="text-[11px] text-slate-500 font-mono self-start sm:self-auto">
                        Akses Penuh Privilege
                      </span>
                    )}
                  </div>

                  {/* Perks list */}
                  {tier.perks && tier.perks.length > 0 && (
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3">
                      {tier.perks.map((perk, i) => {
                        const PerkIcon = getPerkIcon(perk.icon);
                        return (
                          <div
                            key={i}
                            className="flex items-start gap-2 text-xs bg-white/[0.02] p-2.5 rounded-xl border border-white/5"
                          >
                            <div className="p-1 rounded-lg bg-[#CBAC70]/10 text-[#CBAC70] shrink-0 mt-0.5">
                              <PerkIcon className="w-3.5 h-3.5" />
                            </div>
                            <div>
                              <p className="font-bold text-slate-200 text-[11px]">
                                {perk.title}
                              </p>
                              {perk.desc && (
                                <p className="text-[10px] text-slate-400 leading-snug mt-0.5">
                                  {perk.desc}
                                </p>
                              )}
                            </div>
                          </div>
                        );
                      })}
                    </div>
                  )}
                </div>
              );
            })}
          </div>
        </div>

        {/* Footer Actions */}
        <div className="p-4 sm:p-5 border-t border-white/10 bg-[#0A1024] flex items-center justify-between gap-3 shrink-0">
          <button
            type="button"
            onClick={onClose}
            className="px-4 py-2.5 rounded-xl text-xs font-bold text-slate-400 hover:text-white hover:bg-white/5 transition cursor-pointer"
          >
            Tutup
          </button>

          <Link
            href="/katalog"
            onClick={onClose}
            className="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#B39355] text-[#0B132B] font-bold text-xs shadow-lg hover:from-[#E3CD99] hover:to-[#CBAC70] transition flex items-center gap-1.5 active:scale-95 cursor-pointer"
          >
            <span>Mulai Belanja & Naik Tier</span>
            <ArrowRight className="w-4 h-4" />
          </Link>
        </div>
      </div>
    </div>
  );
}
