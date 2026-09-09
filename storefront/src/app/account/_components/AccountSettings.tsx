'use client';

import React, { useState, useEffect } from 'react';
import { User, Mail, Phone, Bell, Check, Loader2, ShieldCheck, AlertCircle } from 'lucide-react';
import { CustomerProfile } from '../../../types';

interface AccountSettingsProps {
  customer: CustomerProfile | null;
  updateProfile: (data: Partial<CustomerProfile>) => Promise<any>;
}

export default function AccountSettings({ customer, updateProfile }: AccountSettingsProps) {
  const [name, setName] = useState(customer?.name || '');
  const [phone, setPhone] = useState(customer?.phone || '');
  const [marketingOptIn, setMarketingOptIn] = useState(customer?.marketing_opt_in ?? true);
  const [isSaving, setIsSaving] = useState(false);
  const [statusMessage, setStatusMessage] = useState<{ type: 'success' | 'error'; text: string } | null>(null);

  // Sync state if customer props update
  useEffect(() => {
    if (customer) {
      setName(customer.name || '');
      setPhone(customer.phone || '');
      setMarketingOptIn(customer.marketing_opt_in ?? true);
    }
  }, [customer]);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setIsSaving(true);
    setStatusMessage(null);

    try {
      const ok = await updateProfile({
        name: name.trim(),
        phone: phone.trim(),
        marketing_opt_in: marketingOptIn,
      });

      if (ok) {
        setStatusMessage({
          type: 'success',
          text: 'Profil dan preferensi berhasil disimpan.',
        });
        setTimeout(() => setStatusMessage(null), 4000);
      } else {
        setStatusMessage({
          type: 'error',
          text: 'Gagal memperbarui profil. Silakan coba kembali.',
        });
      }
    } catch (err) {
      setStatusMessage({
        type: 'error',
        text: 'Terjadi gangguan jaringan saat menyimpan perubahan.',
      });
    } finally {
      setIsSaving(false);
    }
  };

  return (
    <div className="rounded-3xl bg-[#0E1736] border border-white/10 p-5 sm:p-7 shadow-2xl space-y-6">
      <div className="border-b border-white/10 pb-4">
        <h3 className="text-base font-bold text-white flex items-center gap-2">
          <User className="w-4 h-4 text-[#CBAC70]" />
          <span>Informasi Profil & Kontak Member</span>
        </h3>
        <p className="text-xs text-slate-400 mt-1">
          Perbarui data diri dan nomor kontak untuk kelancaran pengiriman pesanan dan notifikasi resi kurir.
        </p>
      </div>

      {statusMessage && (
        <div
          className={`p-4 rounded-2xl text-xs font-semibold flex items-center gap-2.5 transition-all animate-fade-in ${
            statusMessage.type === 'success'
              ? 'bg-emerald-500/15 border border-emerald-500/40 text-emerald-300'
              : 'bg-rose-500/15 border border-rose-500/40 text-rose-300'
          }`}
        >
          {statusMessage.type === 'success' ? (
            <Check className="w-4 h-4 text-emerald-400 shrink-0" />
          ) : (
            <AlertCircle className="w-4 h-4 text-rose-400 shrink-0" />
          )}
          <span>{statusMessage.text}</span>
        </div>
      )}

      <form onSubmit={handleSubmit} className="space-y-5">
        {/* Form Fields Grid */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
          
          {/* 1. Nama Lengkap */}
          <div className="space-y-1.5">
            <label className="text-xs font-bold text-slate-300 block">Nama Lengkap</label>
            <div className="relative flex items-center">
              <div className="absolute left-3.5 text-slate-400 pointer-events-none">
                <User className="w-4 h-4" />
              </div>
              <input
                type="text"
                value={name}
                onChange={(e) => setName(e.target.value)}
                placeholder="Masukkan nama lengkap Anda"
                required
                className="w-full bg-[#070D1F] border border-white/10 focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70]/30 rounded-xl py-2.5 pl-10 pr-4 text-xs sm:text-sm text-white placeholder:text-slate-500 transition-all font-medium"
              />
            </div>
          </div>

          {/* 2. Email (Read-Only / Protected) */}
          <div className="space-y-1.5">
            <div className="flex items-center justify-between">
              <label className="text-xs font-bold text-slate-300">Alamat Email</label>
              <span className="text-[10px] font-mono text-emerald-400 flex items-center gap-1">
                <ShieldCheck className="w-3 h-3" /> Terautentikasi
              </span>
            </div>
            <div className="relative flex items-center">
              <div className="absolute left-3.5 text-slate-500 pointer-events-none">
                <Mail className="w-4 h-4" />
              </div>
              <input
                type="email"
                value={customer?.email || ''}
                disabled
                className="w-full bg-black/40 border border-white/5 rounded-xl py-2.5 pl-10 pr-4 text-xs sm:text-sm text-slate-400 font-mono cursor-not-allowed opacity-80 select-none"
              />
            </div>
          </div>

          {/* 3. Nomor WhatsApp / HP */}
          <div className="space-y-1.5 sm:col-span-2">
            <div className="flex items-center justify-between">
              <label className="text-xs font-bold text-slate-300">
                Nomor WhatsApp Aktif <span className="text-amber-400 font-semibold">*</span>
              </label>
              {!customer?.phone && (
                <span className="text-[10px] font-mono text-amber-400 font-bold">
                  Belum Diatur
                </span>
              )}
            </div>
            <div className="relative flex items-center">
              <div className="absolute left-3.5 text-slate-400 pointer-events-none">
                <Phone className="w-4 h-4" />
              </div>
              <input
                type="tel"
                value={phone}
                onChange={(e) => setPhone(e.target.value)}
                placeholder="Contoh: 081234567890 atau 6281234567890"
                className="w-full bg-[#070D1F] border border-white/10 focus:border-[#CBAC70] focus:ring-1 focus:ring-[#CBAC70]/30 rounded-xl py-2.5 pl-10 pr-4 text-xs sm:text-sm text-white placeholder:text-slate-500 transition-all font-mono"
              />
            </div>
            <p className="text-[11px] text-slate-400 leading-relaxed">
              Nomor WhatsApp digunakan oleh pihak ekspedisi untuk koordinasi alamat paket serta pengiriman link pelacakan resi otomatis.
            </p>
          </div>

        </div>

        {/* 4. Preferensi Pemasaran & Notifikasi */}
        <div className="pt-3 border-t border-white/5 space-y-3">
          <label className="text-xs font-bold text-slate-300 flex items-center gap-2">
            <Bell className="w-3.5 h-3.5 text-[#CBAC70]" />
            <span>Preferensi Pemberitahuan & Diskon</span>
          </label>

          <div className="flex items-start justify-between p-4 rounded-2xl bg-[#070D1F] border border-white/5 gap-3">
            <div className="space-y-0.5 min-w-0">
              <p className="text-xs font-bold text-slate-200">
                Langganan Info Limited Drops & Voucher Eksklusif
              </p>
              <p className="text-[11px] text-slate-400 leading-relaxed">
                Dapatkan informasi awal saat rilisan streetwear terbaru dirilis serta voucher potongan harga khusus member.
              </p>
            </div>
            <input
              type="checkbox"
              id="marketing_opt_in"
              checked={marketingOptIn}
              onChange={(e) => setMarketingOptIn(e.target.checked)}
              className="w-5 h-5 rounded border-slate-700 bg-[#0E1736] text-[#CBAC70] focus:ring-[#CBAC70] cursor-pointer mt-0.5 shrink-0"
            />
          </div>
        </div>

        {/* 5. Submit Button */}
        <div className="pt-2 flex justify-end">
          <button
            type="submit"
            disabled={isSaving}
            className="w-full sm:w-auto px-6 py-3 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:from-[#F2DFB3] hover:to-[#BD9B58] text-[#0B132B] font-bold text-xs shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer disabled:opacity-60"
          >
            {isSaving ? (
              <>
                <Loader2 className="w-4 h-4 animate-spin" />
                <span>Menyimpan Perubahan...</span>
              </>
            ) : (
              <>
                <Check className="w-4 h-4" />
                <span>Simpan Perubahan</span>
              </>
            )}
          </button>
        </div>
      </form>
    </div>
  );
}
