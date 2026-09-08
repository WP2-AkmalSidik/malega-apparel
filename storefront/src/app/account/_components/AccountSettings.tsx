'use client';

import React from 'react';
import { CustomerProfile } from '../../../types';

interface AccountSettingsProps {
  customer: CustomerProfile | null;
  updateProfile: (data: Partial<CustomerProfile>) => Promise<any>;
}

export default function AccountSettings({ customer, updateProfile }: AccountSettingsProps) {
  return (
    <div className="rounded-3xl bg-[#0E1736] border border-white/10 p-6 space-y-6">
      <h3 className="text-base font-bold text-slate-100">Preferensi Akun & Pemasaran</h3>

      <div className="space-y-4">
        <div className="flex items-center justify-between p-4 rounded-2xl bg-[#070D1F] border border-white/5">
          <div className="space-y-1">
            <p className="text-xs font-bold text-slate-200">
              Langganan Notifikasi Rilis Drop & Voucher Promo
            </p>
            <p className="text-[11px] text-slate-400">
              Dapatkan kabar rilis artikel limited edition dan voucher potongan harga langsung via
              WhatsApp & Email.
            </p>
          </div>
          <input
            type="checkbox"
            checked={customer?.marketing_opt_in ?? true}
            onChange={async (e) => {
              await updateProfile({ marketing_opt_in: e.target.checked });
            }}
            className="w-5 h-5 rounded border-slate-700 bg-[#0E1736] text-[#CBAC70] focus:ring-[#CBAC70] cursor-pointer"
          />
        </div>
      </div>
    </div>
  );
}
