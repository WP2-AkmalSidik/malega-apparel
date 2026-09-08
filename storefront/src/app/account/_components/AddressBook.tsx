'use client';

import React from 'react';
import { Plus, X, MapPin } from 'lucide-react';
import { Address } from '../../../types';

interface AddressBookProps {
  savedAddresses?: Address[];
  showAddressModal: boolean;
  setShowAddressModal: (open: boolean) => void;
  addrName: string;
  setAddrName: (v: string) => void;
  addrPhone: string;
  setAddrPhone: (v: string) => void;
  addrStreet: string;
  setAddrStreet: (v: string) => void;
  addrDistrict: string;
  setAddrDistrict: (v: string) => void;
  addrCity: string;
  setAddrCity: (v: string) => void;
  addrProvince: string;
  setAddrProvince: (v: string) => void;
  addrPostal: string;
  setAddrPostal: (v: string) => void;
  handleAddAddress: (e: React.FormEvent) => Promise<void>;
}

export default function AddressBook({
  savedAddresses,
  showAddressModal,
  setShowAddressModal,
  addrName,
  setAddrName,
  addrPhone,
  setAddrPhone,
  addrStreet,
  setAddrStreet,
  addrDistrict,
  setAddrDistrict,
  addrCity,
  setAddrCity,
  addrProvince,
  setAddrProvince,
  addrPostal,
  setAddrPostal,
  handleAddAddress,
}: AddressBookProps) {
  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <h3 className="text-sm font-bold text-slate-200">Alamat Pengiriman Tersimpan</h3>
        <button
          type="button"
          onClick={() => setShowAddressModal(true)}
          className="px-3.5 py-1.5 rounded-xl bg-[#CBAC70] text-[#0B132B] font-bold text-xs shadow flex items-center gap-1.5 transition hover:bg-[#E3CD99] cursor-pointer"
        >
          <Plus className="w-4 h-4" />
          <span>Tambah Alamat Baru</span>
        </button>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        {savedAddresses && savedAddresses.length > 0 ? (
          savedAddresses.map((addr, idx) => (
            <div
              key={idx}
              className="p-5 rounded-3xl bg-[#0E1736] border border-white/10 space-y-2 relative"
            >
              {addr.isDefault && (
                <span className="absolute top-4 right-4 px-2 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                  Utama
                </span>
              )}
              <p className="font-bold text-sm text-slate-100">{addr.name}</p>
              <p className="text-xs text-slate-400 font-mono">{addr.phone}</p>
              <p className="text-xs text-slate-300">{addr.street}</p>
              <p className="text-xs text-slate-400">
                {addr.district}, {addr.city}, {addr.province} {addr.postalCode}
              </p>
            </div>
          ))
        ) : (
          <div className="col-span-2 py-12 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 text-slate-400 text-xs">
            Belum ada alamat tersimpan. Tambahkan alamat untuk checkout lebih cepat.
          </div>
        )}
      </div>

      {/* Add Address Modal */}
      {showAddressModal && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 animate-in fade-in">
          <div className="bg-[#0E1736] border border-[#CBAC70]/40 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 animate-in zoom-in-95 text-[#FDFCFF]">
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div className="flex items-center gap-2">
                <MapPin className="w-4 h-4 text-[#CBAC70]" />
                <h3 className="font-bold text-sm text-[#FDFCFF] uppercase tracking-wider">
                  Tambah Alamat Pengiriman
                </h3>
              </div>
              <button
                onClick={() => setShowAddressModal(false)}
                className="text-[#94A3B8] hover:text-white p-1 cursor-pointer"
              >
                <X className="w-4 h-4" />
              </button>
            </div>

            <form onSubmit={handleAddAddress} className="space-y-3 text-xs">
              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">Nama Penerima</label>
                  <input
                    type="text"
                    value={addrName}
                    onChange={(e) => setAddrName(e.target.value)}
                    placeholder="Nama Penerima"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">No. Handphone</label>
                  <input
                    type="text"
                    value={addrPhone}
                    onChange={(e) => setAddrPhone(e.target.value)}
                    placeholder="08123456789"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
              </div>

              <div>
                <label className="block text-[11px] text-slate-400 mb-1">Alamat Lengkap</label>
                <textarea
                  required
                  rows={2}
                  value={addrStreet}
                  onChange={(e) => setAddrStreet(e.target.value)}
                  placeholder="Nama jalan, nomor rumah, RT/RW, dsb."
                  className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                />
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">Kecamatan</label>
                  <input
                    type="text"
                    required
                    value={addrDistrict}
                    onChange={(e) => setAddrDistrict(e.target.value)}
                    placeholder="Kecamatan"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">Kota / Kabupaten</label>
                  <input
                    type="text"
                    required
                    value={addrCity}
                    onChange={(e) => setAddrCity(e.target.value)}
                    placeholder="Kota / Kabupaten"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-2">
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">Provinsi</label>
                  <input
                    type="text"
                    required
                    value={addrProvince}
                    onChange={(e) => setAddrProvince(e.target.value)}
                    placeholder="Provinsi"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
                <div>
                  <label className="block text-[11px] text-slate-400 mb-1">Kode Pos</label>
                  <input
                    type="text"
                    required
                    value={addrPostal}
                    onChange={(e) => setAddrPostal(e.target.value)}
                    placeholder="Kode Pos"
                    className="w-full bg-[#070D1F] border border-white/10 rounded-xl px-3 py-2 text-white placeholder-slate-500 focus:outline-none focus:border-[#CBAC70]"
                  />
                </div>
              </div>

              <div className="pt-2 flex items-center justify-end gap-2">
                <button
                  type="button"
                  onClick={() => setShowAddressModal(false)}
                  className="px-4 py-2 rounded-xl bg-white/5 hover:bg-white/10 text-slate-300 font-bold text-xs"
                >
                  Batal
                </button>
                <button
                  type="submit"
                  className="px-5 py-2 rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#A58645] text-[#0B132B] font-bold text-xs shadow-lg"
                >
                  Simpan Alamat
                </button>
              </div>
            </form>
          </div>
        </div>
      )}
    </div>
  );
}
