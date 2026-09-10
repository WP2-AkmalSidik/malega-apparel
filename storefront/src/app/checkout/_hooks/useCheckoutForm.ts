'use client';

import { useState } from 'react';
import { Address, CustomerProfile } from '../../../types';

interface UseCheckoutFormOptions {
  selectedAddress: Address;
  setSelectedAddress: (a: Address) => void;
  applyVoucherCodeAsync: (
    code: string,
    email?: string,
    phone?: string
  ) => Promise<{ success: boolean; message: string; discount?: number }>;
  toggleVoucher: (code: string) => void;
  customer?: CustomerProfile | null;
}

export function useCheckoutForm({
  selectedAddress,
  setSelectedAddress,
  applyVoucherCodeAsync,
  toggleVoucher,
  customer,
}: UseCheckoutFormOptions) {
  const [isEditingAddress, setIsEditingAddress] = useState(false);
  const [addressForm, setAddressForm] = useState(selectedAddress);
  const [voucherInput, setVoucherInput] = useState('');
  const [voucherError, setVoucherError] = useState('');
  const [voucherSuccess, setVoucherSuccess] = useState('');
  const [isValidatingPromo, setIsValidatingPromo] = useState(false);
  const [showVoucherModal, setShowVoucherModal] = useState(false);
  const [buyerNoteLocal, setBuyerNoteLocal] = useState('');

  const targetEmail = customer?.email || 'pelanggan@malega.my.id';
  const targetPhone = customer?.phone || selectedAddress.phone || '081234567890';

  const handleSaveAddress = (e: React.FormEvent) => {
    e.preventDefault();
    setSelectedAddress(addressForm);
    setIsEditingAddress(false);
  };

  const handleApplyPromo = async (e: React.FormEvent) => {
    e.preventDefault();
    setVoucherError('');
    setVoucherSuccess('');
    if (!voucherInput.trim()) return;

    setIsValidatingPromo(true);
    try {
      const res = await applyVoucherCodeAsync(voucherInput.trim(), targetEmail, targetPhone);
      if (res.success) {
        setVoucherSuccess(res.message);
        setVoucherInput('');
      } else {
        setVoucherError(res.message || 'Kode voucher tidak valid atau tidak memenuhi syarat.');
      }
    } catch (err: any) {
      setVoucherError('Gagal memvalidasi voucher ke server.');
    } finally {
      setIsValidatingPromo(false);
    }
  };

  const handleSelectVoucherFromModal = async (code: string) => {
    setShowVoucherModal(false);
    setVoucherError('');
    setVoucherSuccess('');
    setIsValidatingPromo(true);
    try {
      const res = await applyVoucherCodeAsync(code, targetEmail, targetPhone);
      if (res.success) {
        setVoucherSuccess(res.message);
      } else {
        setVoucherError(res.message);
      }
    } catch (err: any) {
      setVoucherError('Gagal memvalidasi voucher ke server.');
    } finally {
      setIsValidatingPromo(false);
    }
  };

  return {
    isEditingAddress,
    setIsEditingAddress,
    addressForm,
    setAddressForm,
    voucherInput,
    setVoucherInput,
    voucherError,
    voucherSuccess,
    isValidatingPromo,
    showVoucherModal,
    setShowVoucherModal,
    buyerNoteLocal,
    setBuyerNoteLocal,
    handleSaveAddress,
    handleApplyPromo,
    handleSelectVoucherFromModal,
  };
}
