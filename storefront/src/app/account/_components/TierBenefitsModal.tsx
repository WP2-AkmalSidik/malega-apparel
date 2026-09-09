'use client';

import React, { useState } from 'react';
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

interface TierBenefitsModalProps {
  isOpen: boolean;
  onClose: () => void;
  customer: CustomerProfile | null;
}

export default function TierBenefitsModal({
  isOpen,
  onClose,
  customer,
}: TierBenefitsModalProps) {
  const [copiedCode, setCopiedCode] = useState<string | null>(null);

  if (!isOpen) return null;

  const spend = customer?.total_spend || 0;
  const currentTier = customer?.membership_tier || 'Silver';

  // Copy voucher code helper
  const handleCopyCode = (code: string) => {
    navigator.clipboard.writeText(code);
    setCopiedCode(code);
    setTimeout(() => setCopiedCode(null), 2000);
  };

  // Tier progress calculations
  let nextTierName = 'Gold';
  let remainingSpend = Math.max(0, 500000 - spend);
  let progressPercent = Math.min(100, Math.round((spend / 500000) * 100));

  if (currentTier === 'Gold') {
    nextTierName = 'VIP Platinum';
    remainingSpend = Math.max(0, 1500000 - spend);
    progressPercent = Math.min(
      100,
      Math.round(((spend - 500000) / 1000000) * 100)
    );
  } else if (currentTier === 'VIP Platinum') {
    nextTierName = 'Tingkat Maksimal';
    remainingSpend = 0;
    progressPercent = 100;
  }

  const tiers = [
    {
      id: 'Silver',
      name: 'Silver Member',
      icon: ShieldCheck,
      threshold: 'Rp 0 - Rp 499.999',
      badgeColor: 'bg-slate-800 text-slate-300 border-slate-700',
      tagColor: 'bg-slate-700/50 text-slate-300',
      borderActive: 'border-slate-400 ring-2 ring-slate-400/30',
      voucherCode: 'WELCOME10',
      discountInfo: 'Diskon 10% Sambutan Pembeli Baru',
      perks: [
        {
          icon: Gift,
          title: 'Voucher Sambutan 10%',
          desc: 'Gunakan kode WELCOME10 untuk potongan 10% pembelian pertama.',
        },
        {
          icon: Zap,
          title: 'Realtime Live Tracking',
          desc: 'Pantau status pesanan dan pergerakan resi ekspedisi langsung di web.',
        },
        {
          icon: Sparkles,
          title: 'Akses Rilis Resmi',
          desc: 'Dapatkan pemberitahuan pertama saat katalog baru diluncurkan.',
        },
      ],
    },
    {
      id: 'Gold',
      name: 'Gold Member',
      icon: Sparkles,
      threshold: 'Rp 500.000 - Rp 1.499.999',
      badgeColor: 'bg-amber-500/20 text-amber-300 border-amber-500/40',
      tagColor: 'bg-amber-500/20 text-amber-300',
      borderActive: 'border-amber-400 ring-2 ring-amber-400/40',
      voucherCode: 'MALEGAVIP15',
      discountInfo: 'Diskon 15% Tiap Rilis Koleksi Baru',
      perks: [
        {
          icon: Gift,
          title: 'Diskon 15% New Drop',
          desc: 'Kupon MALEGAVIP15 untuk diskon 15% setiap peluncuran artikel baru.',
        },
        {
          icon: Truck,
          title: 'Bebas / Subsidi Ongkir',
          desc: 'Gratis atau potongan ongkos kirim reguler ke seluruh kota di Indonesia.',
        },
        {
          icon: Zap,
          title: 'Priority Dispatch Packing',
          desc: 'Paket diprioritaskan untuk proses QC dan pengiriman oleh tim gudang.',
        },
      ],
    },
    {
      id: 'VIP Platinum',
      name: 'VIP Platinum Member',
      icon: Crown,
      threshold: '≥ Rp 1.500.000',
      badgeColor: 'bg-[#CBAC70]/20 text-[#CBAC70] border-[#CBAC70]/50',
      tagColor: 'bg-[#CBAC70]/20 text-[#CBAC70]',
      borderActive: 'border-[#CBAC70] ring-2 ring-[#CBAC70]/50 shadow-[0_0_25px_rgba(203,172,112,0.2)]',
      voucherCode: 'MEMBERONLY20',
      discountInfo: 'Diskon 20% All-Item Permanen',
      perks: [
        {
          icon: Crown,
          title: 'Diskon 20% Seumur Hidup',
          desc: 'Kupon MEMBERONLY20 untuk potongan 20% all-item tanpa batas kuota.',
        },
        {
          icon: Clock,
          title: 'Early-Bird Drop 24 Jam',
          desc: 'Beli koleksi limited edition 24 jam lebih awal sebelum dirilis ke publik.',
        },
        {
          icon: Sparkles,
          title: 'VIP WhatsApp Concierge',
          desc: 'Akses nomor WhatsApp personal assist khusus konsultasi size & fast-track order.',
        },
      ],
    },
  ];

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
                Sistem loyalitas otomatis tanpa masa kedaluwarsa
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
                    className={`px-2.5 py-0.5 rounded-full text-xs font-mono font-bold ${
                      currentTier === 'VIP Platinum'
                        ? 'bg-[#CBAC70]/20 text-[#CBAC70] border border-[#CBAC70]/50'
                        : currentTier === 'Gold'
                        ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40'
                        : 'bg-slate-800 text-slate-300 border border-slate-700'
                    }`}
                  >
                    ★ {currentTier} Member
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
            {currentTier !== 'VIP Platinum' ? (
              <div className="mt-4 pt-3 border-t border-white/10 space-y-2">
                <div className="flex items-center justify-between text-xs">
                  <span className="text-slate-300 font-medium flex items-center gap-1.5">
                    <TrendingUp className="w-3.5 h-3.5 text-[#CBAC70]" />
                    <span>Target: <strong>{nextTierName} Member</strong></span>
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
                  lagi untuk membuka level {nextTierName} dan keuntungan eksklusifnya.
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
                <div className="text-amber-400 font-bold font-mono">3. Instan & Langsung</div>
                <p className="text-slate-400 text-[11px] leading-relaxed">
                  Kode voucher eksklusif langsung bisa Anda gunakan saat checkout tanpa perlu klaim manual.
                </p>
              </div>
            </div>
          </div>

          {/* Tier Cards List */}
          <div className="space-y-3">
            <h4 className="text-xs font-mono uppercase tracking-wider text-slate-400 font-bold">
              Daftar Tingkatan & Keuntungan
            </h4>

            {tiers.map((tier) => {
              const isCurrent = currentTier === tier.id;
              const IconComponent = tier.icon;

              return (
                <div
                  key={tier.id}
                  className={`rounded-2xl p-4 sm:p-5 border transition-all ${
                    isCurrent
                      ? `bg-[#0E1736] ${tier.borderActive}`
                      : 'bg-[#0A1024]/60 border-white/10 hover:border-white/20'
                  }`}
                >
                  <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 pb-3 border-b border-white/10">
                    <div className="flex items-center gap-3">
                      <div
                        className={`w-9 h-9 rounded-xl flex items-center justify-center font-bold shrink-0 ${
                          tier.id === 'VIP Platinum'
                            ? 'bg-[#CBAC70] text-[#0B132B]'
                            : tier.id === 'Gold'
                            ? 'bg-amber-500 text-slate-950'
                            : 'bg-slate-700 text-slate-200'
                        }`}
                      >
                        <IconComponent className="w-5 h-5" />
                      </div>
                      <div>
                        <div className="flex items-center gap-2 flex-wrap">
                          <h5 className="font-bold text-white text-sm">
                            {tier.name}
                          </h5>
                          {isCurrent && (
                            <span className="px-2 py-0.5 rounded-full text-[10px] font-mono font-black bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 animate-pulse">
                              ● Tier Anda Saat Ini
                            </span>
                          )}
                        </div>
                        <p className="text-[11px] font-mono text-slate-400">
                          Syarat: Akumulasi Belanja {tier.threshold}
                        </p>
                      </div>
                    </div>

                    {/* Voucher Pill */}
                    <div className="flex items-center gap-1.5 self-start sm:self-auto bg-black/40 px-3 py-1.5 rounded-xl border border-white/10">
                      <span className="text-[10px] font-mono text-slate-400">Kupon:</span>
                      <code className="text-xs font-mono font-black text-[#CBAC70]">
                        {tier.voucherCode}
                      </code>
                      <button
                        type="button"
                        onClick={() => handleCopyCode(tier.voucherCode)}
                        className="text-slate-400 hover:text-white p-0.5 transition cursor-pointer"
                        title="Salin kode kupon"
                      >
                        {copiedCode === tier.voucherCode ? (
                          <Check className="w-3.5 h-3.5 text-emerald-400" />
                        ) : (
                          <Copy className="w-3.5 h-3.5" />
                        )}
                      </button>
                    </div>
                  </div>

                  {/* Perks list */}
                  <div className="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-3">
                    {tier.perks.map((perk, i) => {
                      const PerkIcon = perk.icon;
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
                            <p className="text-[10px] text-slate-400 leading-snug mt-0.5">
                              {perk.desc}
                            </p>
                          </div>
                        </div>
                      );
                    })}
                  </div>
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
