'use client';

import React, { useEffect, useState } from 'react';
import { Ticket, X } from 'lucide-react';
import { Voucher } from '../../../types';

interface VoucherModalProps {
  isOpen: boolean;
  onClose: () => void;
  vouchers: Voucher[];
  appliedVouchers: Voucher[];
  subtotal: number;
  toggleVoucher: (code: string) => void;
  onSelectVoucher: (code: string) => void;
  formatRupiah: (val: number) => string;
}

export const VoucherModal: React.FC<VoucherModalProps> = ({
  isOpen,
  onClose,
  vouchers,
  appliedVouchers,
  subtotal,
  toggleVoucher,
  onSelectVoucher,
  formatRupiah,
}) => {
  const [isRendered, setIsRendered] = useState(false);
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    if (isOpen) {
      setIsRendered(true);
      // Next tick to allow initial styles to apply before transitioning in
      const timer = setTimeout(() => {
        setIsVisible(true);
      }, 20);
      document.body.style.overflow = 'hidden';
      return () => clearTimeout(timer);
    } else {
      setIsVisible(false);
      const timer = setTimeout(() => {
        setIsRendered(false);
      }, 250);
      document.body.style.overflow = '';
      return () => clearTimeout(timer);
    }
  }, [isOpen]);

  useEffect(() => {
    return () => {
      document.body.style.overflow = '';
    };
  }, []);

  const handleClose = () => {
    setIsVisible(false);
    setTimeout(() => {
      onClose();
    }, 250);
  };

  const handleSelect = (code: string) => {
    setIsVisible(false);
    setTimeout(() => {
      onSelectVoucher(code);
    }, 250);
  };

  useEffect(() => {
    const handleKeyDown = (e: KeyboardEvent) => {
      if (e.key === 'Escape' && isRendered) {
        handleClose();
      }
    };
    window.addEventListener('keydown', handleKeyDown);
    return () => window.removeEventListener('keydown', handleKeyDown);
  }, [isRendered]);

  if (!isRendered) return null;

  return (
    <div
      role="dialog"
      aria-modal="true"
      className={`fixed inset-0 z-50 flex items-center justify-center p-4 transition-all duration-250 ease-out ${
        isVisible
          ? 'bg-black/85 backdrop-blur-md opacity-100'
          : 'bg-black/0 backdrop-blur-none opacity-0 pointer-events-none'
      }`}
      onClick={(e) => {
        if (e.target === e.currentTarget) {
          handleClose();
        }
      }}
    >
      <div
        className={`bg-[#111D42] border border-[#CBAC70]/40 rounded-3xl max-w-lg w-full p-5 sm:p-6 shadow-2xl space-y-4 text-[#FDFCFF] max-h-[85vh] flex flex-col transition-all duration-250 ease-out ${
          isVisible
            ? 'scale-100 opacity-100 translate-y-0'
            : 'scale-95 opacity-0 translate-y-4 pointer-events-none'
        }`}
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between border-b border-white/10 pb-3">
          <div className="flex items-center gap-2">
            <Ticket className="w-5 h-5 text-[#CBAC70] shrink-0" />
            <div>
              <h3 className="font-bold text-sm text-[#FDFCFF]">Voucher & Diskon Spesial</h3>
              <p className="text-[10px] text-[#94A3B8]">Pilih voucher eksklusif untuk pesanan Anda</p>
            </div>
          </div>
          <button
            type="button"
            onClick={handleClose}
            className="text-[#94A3B8] hover:text-white cursor-pointer p-1 rounded-lg transition-colors"
            aria-label="Tutup"
          >
            <X className="w-5 h-5" />
          </button>
        </div>

        {/* Voucher List */}
        <div className="flex-1 overflow-y-auto space-y-3 pr-1">
          {vouchers.length === 0 ? (
            <div className="text-center py-8 text-xs text-[#94A3B8]">
              Belum ada voucher aktif yang tersedia saat ini.
            </div>
          ) : (
            vouchers.map((v) => {
              const isApplied = appliedVouchers.some(
                (av) => av.code.toUpperCase() === v.code.toUpperCase()
              );
              const isMinSpendMet = subtotal >= (v.minSpend || v.min_spend || 0);

              return (
                <div
                  key={v.code}
                  className={`p-4 rounded-2xl border transition-all relative ${
                    isApplied
                      ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70]'
                      : isMinSpendMet
                      ? 'bg-[#0B132B] border-white/10 hover:border-[#CBAC70]/50'
                      : 'bg-[#070D1F]/60 border-white/5 opacity-60'
                  }`}
                >
                  <div className="flex items-start justify-between gap-3">
                    <div className="space-y-1 min-w-0">
                      <div className="flex items-center gap-2 flex-wrap">
                        <span className="font-mono text-xs font-black text-[#CBAC70] bg-[#070D1F] px-2 py-0.5 rounded border border-[#CBAC70]/30 tracking-wider">
                          {v.code}
                        </span>
                        <span className="text-xs font-bold text-[#FDFCFF] truncate">
                          {v.title || v.name}
                        </span>
                      </div>
                      {v.description && (
                        <p className="text-[11px] text-[#94A3B8] leading-relaxed">
                          {v.description}
                        </p>
                      )}
                      <div className="flex items-center gap-3 text-[10px] text-[#94A3B8] pt-1">
                        <span>
                          Min. Belanja:{' '}
                          <strong className="text-[#FDFCFF]">
                            {formatRupiah(v.minSpend || v.min_spend || 0)}
                          </strong>
                        </span>
                        {v.formatted_discount && (
                          <span className="text-[#CBAC70] font-semibold">
                            • {v.formatted_discount}
                          </span>
                        )}
                      </div>
                    </div>

                    <button
                      type="button"
                      onClick={() => {
                        if (isApplied) {
                          toggleVoucher(v.code);
                        } else {
                          handleSelect(v.code);
                        }
                      }}
                      disabled={!isMinSpendMet && !isApplied}
                      className={`px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shrink-0 ${
                        isApplied
                          ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 hover:bg-rose-500/30'
                          : isMinSpendMet
                          ? 'bg-[#CBAC70] text-[#0B132B] hover:bg-[#E3CD99] shadow'
                          : 'bg-white/5 text-white/30 border border-white/5 cursor-not-allowed'
                      }`}
                    >
                      {isApplied ? 'Batalkan' : isMinSpendMet ? 'Gunakan' : 'S&K Belum Pas'}
                    </button>
                  </div>
                </div>
              );
            })
          )}
        </div>

        {/* Footer */}
        <div className="pt-2 border-t border-white/10 flex justify-end">
          <button
            type="button"
            onClick={handleClose}
            className="px-4 py-2 bg-white/10 hover:bg-white/15 text-xs font-bold text-[#FDFCFF] rounded-xl cursor-pointer transition-colors"
          >
            Tutup
          </button>
        </div>
      </div>
    </div>
  );
};
