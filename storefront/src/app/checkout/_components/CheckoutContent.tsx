'use client';

import React from 'react';
import Link from 'next/link';
import {
  MapPin,
  Truck,
  Ticket,
  CreditCard,
  ShieldCheck,
  ArrowRight,
  QrCode,
  Copy,
  Check,
  ShoppingBag,
  ArrowLeft,
  Lock,
  Sparkles,
  Loader2,
  RefreshCw,
  ExternalLink,
  Zap,
  Info,
} from 'lucide-react';
import { useCart } from '../../../context/CartContext';
import { formatRupiah } from '../../../lib/utils';
import PaymentLogo from '../../../components/PaymentLogo';
import { useShippingRates } from '../_hooks/useShippingRates';
import { usePaymentMethods } from '../_hooks/usePaymentMethods';
import { useCheckoutForm } from '../_hooks/useCheckoutForm';
import { useCheckoutSubmit } from '../_hooks/useCheckoutSubmit';
import { VoucherModal } from './VoucherModal';

export default function CheckoutContent() {
  const {
    cart,
    checkoutItems,
    checkoutCount,
    isInstantBuyActive,
    clearInstantBuy,
    selectedAddress,
    setSelectedAddress,
    selectedShipping,
    setSelectedShipping,
    selectedPayment,
    setSelectedPayment,
    vouchers,
    appliedVouchers,
    toggleVoucher,
    applyVoucherCode,
    applyVoucherCodeAsync,
    buyerNote,
    setBuyerNote,
    subtotal,
    shippingCost,
    shippingDiscount,
    productDiscount,
    serviceFee,
    grandTotal,
    createOrder,
    clearCart,
  } = useCart();

  // Hooks
  const { couriersList, isLoadingRates } = useShippingRates({
    postalCode: selectedAddress.postalCode,
    city: selectedAddress.city,
    checkoutItems,
    selectedShipping,
    setSelectedShipping,
  });

  const { paymentsList, isLoadingPayments } = usePaymentMethods({
    subtotal,
    selectedPayment,
    setSelectedPayment,
  });

  const {
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
    handleSaveAddress,
    handleApplyPromo,
    handleSelectVoucherFromModal,
  } = useCheckoutForm({
    selectedAddress,
    setSelectedAddress,
    applyVoucherCodeAsync,
    toggleVoucher,
  });

  const {
    isProcessing,
    showPaymentModal,
    setShowPaymentModal,
    livePaymentResult,
    handleProceedPayment,
    handleConfirmOrderFinal,
  } = useCheckoutSubmit({
    checkoutItems,
    selectedAddress,
    selectedShipping,
    selectedPayment,
    appliedVouchers,
    shippingCost,
    serviceFee,
    productDiscount,
    shippingDiscount,
    grandTotal,
    buyerNote,
    createOrder,
  });

  // Empty cart state
  if (checkoutItems.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-16 text-center space-y-4">
        <div className="w-16 h-16 rounded-2xl bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center mx-auto">
          <ShoppingBag className="w-8 h-8" />
        </div>
        <h2 className="text-lg font-bold text-[#FDFCFF]">
          {cart.length > 0 ? 'Tidak Ada Produk yang Dipilih' : 'Shopping Bag Kosong'}
        </h2>
        <p className="text-xs text-[#94A3B8]">
          {cart.length > 0
            ? 'Semua produk di keranjang Anda dalam status tidak dipilih. Silakan kembali ke keranjang untuk memilih produk yang ingin Anda beli.'
            : 'Silakan pilih artikel apparel favorit Anda terlebih dahulu.'}
        </p>
        <div className="flex items-center justify-center gap-3 pt-2">
          <Link
            href="/"
            className="inline-block px-6 py-3 bg-[#CBAC70] text-[#0B132B] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#E3CD99] shadow cursor-pointer"
          >
            Lihat Katalog
          </Link>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8 py-4 sm:py-8 space-y-6">
      {/* Top Header */}
      <div className="flex items-center justify-between border-b border-white/10 pb-3">
        <div className="space-y-1">
          <Link href="/" className="text-xs text-[#94A3B8] hover:text-[#CBAC70] flex items-center gap-1">
            <ArrowLeft className="w-3.5 h-3.5" /> Kembali Belanja
          </Link>
          <h1 className="text-xl sm:text-2xl font-black text-[#FDFCFF] uppercase tracking-wide flex items-center gap-2">
            <span>Secure Checkout</span>
            <Lock className="w-4 h-4 text-[#CBAC70]" />
          </h1>
        </div>
        <div className="flex items-center gap-2">
          <span className="text-[11px] text-[#CBAC70] font-mono hidden sm:inline bg-[#14204A] px-2.5 py-1 rounded-lg border border-[#CBAC70]/30">
            🔒 256-Bit SSL Encrypted • Verified Checkout
          </span>
        </div>
      </div>

      {/* Main Checkout Columns */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        {/* Left Column: Forms */}
        <div className="lg:col-span-7 space-y-4 sm:space-y-6">
          {/* Step 1: Delivery Address */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <div className="flex items-center justify-between border-b border-white/10 pb-2.5">
                <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2">
                  <MapPin className="w-4 h-4" /> 1. Alamat Pengiriman
                </h3>
                <button
                  type="button"
                  onClick={() => setIsEditingAddress(!isEditingAddress)}
                  className="text-xs text-[#CBAC70] font-bold hover:underline cursor-pointer"
                >
                  {isEditingAddress ? 'Batal' : 'Ubah Alamat'}
                </button>
              </div>

              {isEditingAddress ? (
                <form onSubmit={handleSaveAddress} className="space-y-3 text-xs">
                  <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Nama Penerima</label>
                      <input
                        type="text"
                        value={addressForm.name}
                        onChange={(e) => setAddressForm({ ...addressForm, name: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Nomor WhatsApp</label>
                      <input
                        type="text"
                        value={addressForm.phone}
                        onChange={(e) => setAddressForm({ ...addressForm, phone: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                  </div>

                  <div>
                    <label className="block text-[#94A3B8] mb-1 font-semibold">Alamat Lengkap (Jalan / Gedung / No)</label>
                    <input
                      type="text"
                      value={addressForm.street}
                      onChange={(e) => setAddressForm({ ...addressForm, street: e.target.value })}
                      className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                      required
                    />
                  </div>

                  <div className="grid grid-cols-3 gap-2">
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kecamatan</label>
                      <input
                        type="text"
                        value={addressForm.district}
                        onChange={(e) => setAddressForm({ ...addressForm, district: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kota / Kab</label>
                      <input
                        type="text"
                        value={addressForm.city}
                        onChange={(e) => setAddressForm({ ...addressForm, city: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kode Pos (5 Digit)</label>
                      <input
                        type="text"
                        value={addressForm.postalCode}
                        onChange={(e) => setAddressForm({ ...addressForm, postalCode: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        placeholder="12730"
                        required
                      />
                    </div>
                  </div>

                  <button
                    type="submit"
                    className="px-4 py-2 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-xl hover:bg-[#E3CD99] transition-all cursor-pointer"
                  >
                    Simpan Alamat & Hitung Ongkir
                  </button>
                </form>
              ) : (
                <div className="text-xs space-y-1 bg-[#0B132B] p-3.5 rounded-xl border border-white/5">
                  <div className="flex items-center gap-2">
                    <span className="font-bold text-[#FDFCFF]">{selectedAddress.name}</span>
                    <span className="text-[#94A3B8]">({selectedAddress.phone})</span>
                    <span className="bg-[#CBAC70]/20 text-[#CBAC70] text-[9px] font-bold px-2 py-0.5 rounded">
                      UTAMA
                    </span>
                  </div>
                  <p className="text-[#94A3B8] text-[11px] leading-relaxed">
                    {selectedAddress.street}, {selectedAddress.district}, {selectedAddress.city} {selectedAddress.postalCode}
                  </p>
                </div>
              )}
            </div>
          </div>

          {/* Step 2: Courier Selection */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <div className="flex items-center justify-between gap-2 border-b border-white/10 pb-2.5">
                <h3 className="font-bold text-[11px] sm:text-xs uppercase tracking-wider text-[#CBAC70] flex items-center gap-1.5 sm:gap-2 whitespace-nowrap min-w-0">
                  <Truck className="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" />
                  <span className="truncate">2. Pilihan Kurir Pengiriman</span>
                </h3>
                <div className="flex items-center gap-1.5 text-[10px] text-[#CBAC70] shrink-0">
                  {isLoadingRates ? (
                    <span className="flex items-center gap-1 text-[#CBAC70] text-[9px] sm:text-[10px] whitespace-nowrap font-medium">
                      <Loader2 className="w-3 h-3 animate-spin shrink-0" /> Menghitung ongkir...
                    </span>
                  ) : (
                    <span className="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded text-[9px] font-bold whitespace-nowrap">
                      ✓ Tarif Otomatis
                    </span>
                  )}
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                {couriersList.map((courier) => {
                  const isSelected = selectedShipping.id === courier.id || selectedShipping.name === courier.name;
                  const isDisabled = courier.disabled === true || courier.available === false;

                  return (
                    <div
                      key={courier.id}
                      onClick={() => {
                        if (!isDisabled) {
                          setSelectedShipping(courier);
                        }
                      }}
                      className={`p-3 rounded-xl border transition-all relative ${
                        isDisabled
                          ? 'opacity-45 bg-[#070D1F]/50 border-red-500/20 cursor-not-allowed select-none'
                          : isSelected
                          ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md cursor-pointer'
                          : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8] cursor-pointer'
                      }`}
                    >
                      <div className="flex justify-between items-start">
                        <div className="min-w-0 pr-2">
                          <div className="flex items-center gap-1.5 flex-wrap">
                            <span className={`font-bold block truncate ${isDisabled ? 'text-[#94A3B8] line-through decoration-red-400/50' : 'text-[#FDFCFF]'}`}>
                              {courier.name}
                            </span>
                            {isDisabled && (
                              <span className="bg-red-500/15 text-red-400 border border-red-500/30 text-[8px] font-bold px-1.5 py-0.2 rounded uppercase tracking-tight">
                                🚫 Di Luar Jangkauan
                              </span>
                            )}
                          </div>
                          <span className="text-[10px] text-[#CBAC70] font-mono block mt-0.5">{courier.courier}</span>
                        </div>
                        <span className={`font-black shrink-0 ${isDisabled ? 'text-[#64748B]' : 'text-[#CBAC70]'}`}>
                          {isDisabled ? '—' : formatRupiah(courier.cost)}
                        </span>
                      </div>
                      <p className={`text-[10px] mt-1 ${isDisabled ? 'text-red-400/90 font-medium' : 'text-[#94A3B8]'}`}>
                        {isDisabled ? courier.disabledReason || 'Alamat di luar area jangkauan instan (Khusus Jabodetabek)' : courier.etd}
                      </p>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          {/* Step 3: Payment Gateway */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <div className="flex items-center justify-between gap-2 border-b border-white/10 pb-2.5">
                <h3 className="font-bold text-[11px] sm:text-xs uppercase tracking-wider text-[#CBAC70] flex items-center gap-1.5 sm:gap-2 whitespace-nowrap min-w-0">
                  <CreditCard className="w-3.5 h-3.5 sm:w-4 sm:h-4 shrink-0" />
                  <span className="truncate">3. Metode Pembayaran</span>
                </h3>
                <span className="bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30 px-2 py-0.5 rounded text-[9px] font-bold whitespace-nowrap shrink-0">
                  ⚡ Pembayaran Otomatis
                </span>
              </div>

              <div className="space-y-2 text-xs">
                {paymentsList.map((pay) => {
                  const isSelected = selectedPayment.id === pay.id || selectedPayment.duitkuCode === pay.duitkuCode;

                  return (
                    <div
                      key={pay.id}
                      onClick={() => setSelectedPayment(pay)}
                      className={`p-3 rounded-xl border cursor-pointer flex items-center justify-between transition-all ${
                        isSelected
                          ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md'
                          : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8]'
                      }`}
                    >
                      <div className="flex items-center gap-3 min-w-0 pr-2">
                        <PaymentLogo
                          code={pay.duitkuCode}
                          category={pay.category}
                          imageUrl={pay.image}
                          name={pay.name}
                        />
                        <div className="space-y-0.5 min-w-0">
                          <div className="flex items-center gap-2 flex-wrap">
                            <span className="font-bold text-[#FDFCFF]">{pay.name}</span>
                            {pay.duitkuCode && (
                              <span className="text-[9px] font-mono text-[#CBAC70] bg-[#070D1F] px-1.5 py-0.2 rounded border border-white/10">
                                {pay.duitkuCode}
                              </span>
                            )}
                          </div>
                          <p className="text-[10px] text-[#94A3B8] line-clamp-1">{pay.description}</p>
                        </div>
                      </div>

                      <div
                        className={`w-4 h-4 rounded-full border flex items-center justify-center shrink-0 ${
                          isSelected ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B]' : 'border-white/30'
                        }`}
                      >
                        {isSelected && <Check className="w-2.5 h-2.5 stroke-[3]" />}
                      </div>
                    </div>
                  );
                })}
              </div>
            </div>
          </div>

          {/* Notes Card */}
          <div className="p-4 rounded-2xl bg-[#080E20] border border-white/10 space-y-1.5">
            <span className="text-xs font-bold text-[#FDFCFF] block">Catatan Pesanan:</span>
            <input
              type="text"
              value={buyerNote}
              onChange={(e) => setBuyerNote(e.target.value)}
              placeholder="Tinggalkan catatan untuk tim packing..."
              className="w-full bg-[#070D1F] border border-white/15 rounded-xl px-3.5 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
            />
          </div>
        </div>

        {/* Right Column: Order Summary */}
        <div className="lg:col-span-5 space-y-4 sm:space-y-6 sticky top-24">
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3.5 text-xs">
              <div className="border-b border-white/10 pb-2.5 flex items-center justify-between gap-2">
                <div className="flex items-center gap-1.5 min-w-0">
                  <h3 className="font-bold text-[11px] sm:text-xs uppercase tracking-wider text-[#CBAC70] whitespace-nowrap">
                    Rincian Pesanan
                  </h3>
                  {isInstantBuyActive && (
                    <span className="bg-amber-400/20 text-amber-300 border border-amber-400/40 text-[9px] px-1.5 py-0.5 rounded font-bold tracking-wider whitespace-nowrap shrink-0">
                      ⚡ Instant Buy
                    </span>
                  )}
                </div>
                <span className="text-[10px] sm:text-xs text-[#94A3B8] font-mono whitespace-nowrap shrink-0">
                  {checkoutItems.length} Produk ({checkoutCount} pcs)
                </span>
              </div>

              <div className="divide-y divide-white/5 max-h-56 overflow-y-auto space-y-2 pr-1">
                {checkoutItems.map((item) => (
                  <div key={item.id} className="pt-2 flex items-center justify-between gap-2.5">
                    <div className="flex items-center gap-2.5 min-w-0">
                      <img src={item.image} alt={item.title} className="w-11 h-13 rounded-lg object-cover border border-white/10 shrink-0" />
                      <div className="min-w-0">
                        <h4 className="font-bold text-[#FDFCFF] truncate">{item.title}</h4>
                        <p className="text-[10px] text-[#94A3B8]">{item.color} • Size {item.size} (x{item.quantity})</p>
                      </div>
                    </div>
                    <span className="font-black text-[#CBAC70] text-xs whitespace-nowrap">
                      {formatRupiah(item.price * item.quantity)}
                    </span>
                  </div>
                ))}
              </div>

              {/* Voucher Promo Input */}
              <div className="pt-2.5 border-t border-white/10 space-y-1.5">
                <div className="flex items-center justify-between">
                  <span className="font-bold text-[#FDFCFF] block">Kode Promo / VIP Voucher:</span>
                  <button
                    type="button"
                    onClick={() => setShowVoucherModal(true)}
                    className="text-[10px] text-[#CBAC70] hover:text-[#E3CD99] font-bold flex items-center gap-1 cursor-pointer transition-colors"
                  >
                    <Ticket className="w-3 h-3" />
                    <span>Pilih Voucher ({vouchers.length})</span>
                  </button>
                </div>
                <form onSubmit={handleApplyPromo} className="flex gap-1.5">
                  <input
                    type="text"
                    value={voucherInput}
                    onChange={(e) => setVoucherInput(e.target.value)}
                    placeholder="MALEGAVIP15"
                    className="flex-1 bg-[#0B132B] border border-white/20 rounded-xl px-3 py-1.5 text-xs uppercase font-mono text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
                  />
                  <button
                    type="submit"
                    disabled={isValidatingPromo}
                    className="px-3 py-1.5 bg-[#14204A] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] font-bold text-xs rounded-xl border border-[#CBAC70]/40 transition-colors cursor-pointer disabled:opacity-50 flex items-center gap-1.5"
                  >
                    {isValidatingPromo ? <Loader2 className="w-3.5 h-3.5 animate-spin" /> : 'Terapkan'}
                  </button>
                </form>

                {voucherError && <p className="text-red-400 text-[10px]">{voucherError}</p>}
                {voucherSuccess && <p className="text-emerald-400 text-[10px]">{voucherSuccess}</p>}

                <div className="flex flex-wrap gap-1 pt-1">
                  {appliedVouchers.map((v) => (
                    <span key={v.code} className="inline-flex items-center gap-1 bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30 px-2 py-0.5 rounded text-[9px] font-bold">
                      <span>✓ {v.title}</span>
                      <button onClick={() => toggleVoucher(v.code)} className="hover:text-white ml-0.5 cursor-pointer">✕</button>
                    </span>
                  ))}
                </div>
              </div>

              {/* Price Breakdown */}
              <div className="pt-2.5 border-t border-white/10 space-y-1.5 text-xs text-[#94A3B8]">
                <div className="flex justify-between">
                  <span>Subtotal Produk</span>
                  <span className="text-[#FDFCFF] font-semibold">{formatRupiah(subtotal)}</span>
                </div>
                <div className="flex justify-between">
                  <span className="flex items-center gap-1">
                    <span>Biaya Pengiriman</span>
                    <span className="text-[10px] text-[#CBAC70]">({selectedShipping.courier})</span>
                  </span>
                  <span className="text-[#FDFCFF] font-semibold">{formatRupiah(shippingCost)}</span>
                </div>
                {shippingDiscount > 0 && (
                  <div className="flex justify-between text-emerald-400">
                    <span>Diskon Ongkir</span>
                    <span>-{formatRupiah(shippingDiscount)}</span>
                  </div>
                )}
                {productDiscount > 0 && (
                  <div className="flex justify-between text-[#CBAC70]">
                    <span>Potongan Voucher</span>
                    <span>-{formatRupiah(productDiscount)}</span>
                  </div>
                )}
                <div className="flex justify-between">
                  <span>Biaya Layanan</span>
                  <span className="text-[#FDFCFF] font-semibold">{formatRupiah(serviceFee)}</span>
                </div>

                <div className="border-t border-white/10 pt-2.5 flex justify-between items-baseline font-bold">
                  <span className="text-xs text-[#FDFCFF]">Total Tagihan:</span>
                  <span className="text-xl font-black text-[#CBAC70] gold-gradient-pure">
                    {formatRupiah(grandTotal)}
                  </span>
                </div>
              </div>

              {/* Pay Action Button */}
              <button
                type="button"
                onClick={handleProceedPayment}
                disabled={isProcessing}
                className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl shadow-xl flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50 cursor-pointer"
              >
                {isProcessing ? (
                  <span className="flex items-center gap-2">
                    <Loader2 className="w-4 h-4 animate-spin" /> Menyiapkan Pembayaran...
                  </span>
                ) : (
                  <>
                    <span>Bayar Sekarang ({formatRupiah(grandTotal)})</span>
                    <ArrowRight className="w-3.5 h-3.5" />
                  </>
                )}
              </button>
            </div>
          </div>
        </div>
      </div>

      {/* Animated Voucher Selection Modal */}
      <VoucherModal
        isOpen={showVoucherModal}
        onClose={() => setShowVoucherModal(false)}
        vouchers={vouchers}
        appliedVouchers={appliedVouchers}
        subtotal={subtotal}
        toggleVoucher={toggleVoucher}
        onSelectVoucher={handleSelectVoucherFromModal}
        formatRupiah={formatRupiah}
      />

      {/* Payment Modal */}
      {showPaymentModal && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 text-[#FDFCFF]">
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div>
                <span className="text-[9px] font-mono text-[#CBAC70] uppercase tracking-widest block font-bold">OFFICIAL PAYMENT GATEWAY</span>
                <h3 className="font-bold text-sm text-[#FDFCFF]">{selectedPayment.name}</h3>
              </div>
              <button onClick={() => setShowPaymentModal(false)} className="text-[#94A3B8] hover:text-white cursor-pointer">✕</button>
            </div>

            {livePaymentResult?.payment_url && (
              <div className="p-3 bg-[#070D1F] border border-[#CBAC70]/30 rounded-xl space-y-2 text-center">
                <p className="text-xs text-[#94A3B8]">Tagihan pembayaran resmi telah terbit:</p>
                <a
                  href={livePaymentResult.payment_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-1.5 px-4 py-2 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-xl hover:bg-[#E3CD99]"
                >
                  <span>Buka Halaman Pembayaran</span>
                  <ExternalLink className="w-3.5 h-3.5" />
                </a>
              </div>
            )}

            {selectedPayment.category === 'qris' && (
              <div className="space-y-3 text-center">
                <p className="text-xs text-[#94A3B8]">Scan QRIS menggunakan aplikasi Mobile Banking atau E-Wallet:</p>
                <div className="w-44 h-44 bg-white p-3 rounded-2xl mx-auto flex items-center justify-center shadow-lg border-2 border-[#CBAC70]">
                  <div className="w-full h-full bg-[#0B132B] rounded flex flex-col items-center justify-center text-white p-2">
                    <QrCode className="w-24 h-24 text-white" />
                    <span className="text-[8px] font-mono text-[#CBAC70] font-bold mt-1">MALEGA APPAREL QRIS</span>
                  </div>
                </div>
                <div className="text-xs">
                  <span className="text-[#94A3B8]">Total:</span>
                  <span className="text-lg font-black text-[#CBAC70] block">{formatRupiah(grandTotal)}</span>
                </div>
              </div>
            )}

            {selectedPayment.category === 'va' && (
              <div className="space-y-3">
                <p className="text-xs text-[#94A3B8]">Transfer ke nomor Virtual Account resmi berikut:</p>
                <div className="p-3.5 rounded-xl bg-[#0B132B] border border-[#CBAC70]/30 space-y-1.5">
                  <div className="flex justify-between text-xs text-[#94A3B8]">
                    <span>Bank:</span>
                    <span className="font-bold text-white">{selectedPayment.bankName || selectedPayment.name}</span>
                  </div>
                  <div className="flex justify-between items-center pt-1 border-t border-white/5">
                    <span className="font-mono text-base font-black text-[#CBAC70] tracking-wider">
                      {livePaymentResult?.va_number || selectedPayment.accountNumber || '8271081234567890'}
                    </span>
                    <button
                      type="button"
                      onClick={() => {
                        navigator.clipboard.writeText(livePaymentResult?.va_number || selectedPayment.accountNumber || '8271081234567890');
                        alert('Nomor VA disalin!');
                      }}
                      className="text-xs font-bold text-[#CBAC70] hover:underline flex items-center gap-1 cursor-pointer"
                    >
                      <Copy className="w-3 h-3" /> Salin
                    </button>
                  </div>
                </div>
                <div className="text-xs flex justify-between">
                  <span className="text-[#94A3B8]">Total Tagihan:</span>
                  <span className="font-black text-[#CBAC70]">{formatRupiah(grandTotal)}</span>
                </div>
              </div>
            )}

            {(selectedPayment.category === 'card' || selectedPayment.category === 'cod') && (
              <div className="space-y-2 text-xs text-[#94A3B8]">
                <p>
                  {selectedPayment.category === 'cod'
                    ? 'Pesanan akan dikemas & dikirim via kurir ekspedisi. Mohon siapkan pembayaran tunai saat kurir tiba di alamat tujuan.'
                    : 'Transaksi terproteksi dengan standar keamanan enkripsi perbankan 256-Bit SSL.'}
                </p>
                <div className="p-3 rounded-xl bg-[#0B132B] border border-white/5 flex justify-between">
                  <span>Total:</span>
                  <span className="font-black text-[#CBAC70]">{formatRupiah(grandTotal)}</span>
                </div>
              </div>
            )}

            <button
              type="button"
              onClick={handleConfirmOrderFinal}
              className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:opacity-95 cursor-pointer"
            >
              Saya Sudah Melakukan Pembayaran
            </button>
          </div>
        </div>
      )}
    </div>
  );
}
