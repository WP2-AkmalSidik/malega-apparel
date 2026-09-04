'use client';

import React, { useState, useEffect } from 'react';
import Link from 'next/link';
import { useRouter } from 'next/navigation';
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
  Info
} from 'lucide-react';
import { useCart } from '../../context/CartContext';
import { shippingCouriers, paymentGateways } from '../../data/products';
import { fetchShippingRatesFromApi, fetchPaymentMethodsFromApi } from '../../lib/api';
import { ShippingOption, PaymentMethod } from '../../types';

export default function CheckoutPage() {
  const router = useRouter();
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
    buyerNote,
    setBuyerNote,
    subtotal,
    shippingCost,
    shippingDiscount,
    productDiscount,
    serviceFee,
    grandTotal,
    createOrder,
    clearCart
  } = useCart();

  const [isEditingAddress, setIsEditingAddress] = useState(false);
  const [addressForm, setAddressForm] = useState(selectedAddress);
  const [voucherInput, setVoucherInput] = useState('');
  const [voucherError, setVoucherError] = useState('');
  const [voucherSuccess, setVoucherSuccess] = useState('');
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  // Dynamic Live Biteship & Duitku State
  const [couriersList, setCouriersList] = useState<ShippingOption[]>(shippingCouriers);
  const [isLoadingRates, setIsLoadingRates] = useState<boolean>(false);
  const [shippingSource, setShippingSource] = useState<'biteship' | 'default'>('default');

  const [paymentsList, setPaymentsList] = useState<PaymentMethod[]>(paymentGateways);
  const [isLoadingPayments, setIsLoadingPayments] = useState<boolean>(false);

  const [livePaymentResult, setLivePaymentResult] = useState<{
    payment_url?: string | null;
    reference?: string | null;
    va_number?: string | null;
    qr_string?: string | null;
  } | null>(null);

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  // 1. Fetch live Biteship rates when address / postal code changes
  useEffect(() => {
    let isMounted = true;
    async function loadShippingRates() {
      if (!selectedAddress.postalCode) return;
      setIsLoadingRates(true);

      try {
        const rates = await fetchShippingRatesFromApi({
          destination_postal_code: selectedAddress.postalCode,
          destination_city: selectedAddress.city,
          items: checkoutItems.map(item => ({
            weight: 350,
            quantity: item.quantity,
          })),
        });

        if (isMounted && rates && rates.length > 0) {
          setCouriersList(rates);
          setShippingSource('biteship');

          // If current selected courier is not in rates, default to first available
          const exists = rates.find(r => r.id === selectedShipping.id || r.courier === selectedShipping.courier);
          if (exists) {
            setSelectedShipping(exists);
          } else {
            setSelectedShipping(rates[0]);
          }
        }
      } catch (err) {
        console.warn('Failed to fetch Biteship live rates:', err);
      } finally {
        if (isMounted) setIsLoadingRates(false);
      }
    }

    loadShippingRates();

    return () => {
      isMounted = false;
    };
  }, [selectedAddress.postalCode, selectedAddress.city, checkoutItems.length]);

  // 2. Fetch live Duitku Payment Methods
  useEffect(() => {
    let isMounted = true;
    async function loadPaymentMethods() {
      setIsLoadingPayments(true);
      try {
        const methods = await fetchPaymentMethodsFromApi(Math.max(10000, subtotal || 100000));
        if (isMounted && methods && methods.length > 0) {
          setPaymentsList(methods);

          // Preserve selected or default to first method
          const exists = methods.find(m => m.id === selectedPayment.id || m.duitkuCode === selectedPayment.duitkuCode);
          if (exists) {
            setSelectedPayment(exists);
          } else {
            setSelectedPayment(methods[0]);
          }
        }
      } catch (err) {
        console.warn('Failed to fetch Duitku payment methods:', err);
      } finally {
        if (isMounted) setIsLoadingPayments(false);
      }
    }

    loadPaymentMethods();

    return () => {
      isMounted = false;
    };
  }, [subtotal]);

  const handleSaveAddress = (e: React.FormEvent) => {
    e.preventDefault();
    setSelectedAddress(addressForm);
    setIsEditingAddress(false);
  };

  const handleApplyPromo = (e: React.FormEvent) => {
    e.preventDefault();
    setVoucherError('');
    setVoucherSuccess('');
    if (!voucherInput.trim()) return;

    const success = applyVoucherCode(voucherInput);
    if (success) {
      setVoucherSuccess(`Voucher ${voucherInput.toUpperCase()} berhasil diterapkan!`);
      setVoucherInput('');
    } else {
      setVoucherError('Kode voucher tidak valid atau telah kedaluwarsa.');
    }
  };

  const handleProceedPayment = async () => {
    setIsProcessing(true);
    try {
      const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api/v1';

      // Resolve Duitku payment method code
      const paymentMethodCode = selectedPayment.duitkuCode 
        || (selectedPayment.category === 'qris' ? 'SP' : selectedPayment.category === 'card' ? 'VC' : 'BC');

      const payload = {
        customer: {
          name: selectedAddress.name || 'Pelanggan Malega',
          email: 'pelanggan@malega.my.id',
          phone: selectedAddress.phone || '081234567890'
        },
        shipping_address: {
          recipient_name: selectedAddress.name || 'Pelanggan Malega',
          phone: selectedAddress.phone || '081234567890',
          address_line1: selectedAddress.street || 'Jl. Malega No. 1',
          address_line2: selectedAddress.district || '',
          city: selectedAddress.city || 'Jakarta Selatan',
          province: selectedAddress.province || 'DKI Jakarta',
          postal_code: selectedAddress.postalCode || '12730',
          courier_name: `${selectedShipping.courier} (${selectedShipping.service})`
        },
        items: checkoutItems.map(item => ({
          variant_id: item.variantId || item.id,
          sku: item.sku || item.slug,
          product_name: item.title,
          variant_title: `${item.color} / ${item.size}`,
          unit_price: item.price,
          quantity: item.quantity
        })),
        payment_method: paymentMethodCode,
        shipping_total: shippingCost,
        discount_total: productDiscount + shippingDiscount,
        notes: buyerNote || 'Pesanan dari Storefront Malega'
      };

      const res = await fetch(`${apiUrl}/orders/checkout`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      if (data.success) {
        // Simpan pesanan di state & bersihkan sesi checkout
        createOrder();

        if (data.payment?.payment_url) {
          // Direct Redirection to Duitku Official Gateway Checkout
          window.location.href = data.payment.payment_url;
          return;
        }

        // Fallback info modal with live payment data
        setLivePaymentResult(data.payment || null);
        setIsProcessing(false);
        setShowPaymentModal(true);
        return;
      }

      setIsProcessing(false);
      setShowPaymentModal(true);
    } catch (err) {
      console.error('Checkout API error:', err);
      setIsProcessing(false);
      setShowPaymentModal(true);
    }
  };

  const handleConfirmOrderFinal = () => {
    setShowPaymentModal(false);
    createOrder();
    router.push('/order-confirmation');
  };

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
            🔒 Duitku 256-Bit SSL • Biteship Integrated
          </span>
        </div>
      </div>

      {/* Main Checkout Columns */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        {/* Left Column: Forms */}
        <div className="lg:col-span-7 space-y-4 sm:space-y-6">
          
          {/* Step 1: Delivery Address with Double Card */}
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
                        onChange={e => setAddressForm({ ...addressForm, name: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Nomor WhatsApp</label>
                      <input
                        type="text"
                        value={addressForm.phone}
                        onChange={e => setAddressForm({ ...addressForm, phone: e.target.value })}
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
                      onChange={e => setAddressForm({ ...addressForm, street: e.target.value })}
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
                        onChange={e => setAddressForm({ ...addressForm, district: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kota / Kab</label>
                      <input
                        type="text"
                        value={addressForm.city}
                        onChange={e => setAddressForm({ ...addressForm, city: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kode Pos (5 Digit)</label>
                      <input
                        type="text"
                        value={addressForm.postalCode}
                        onChange={e => setAddressForm({ ...addressForm, postalCode: e.target.value })}
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

          {/* Step 2: Courier Selection with Live Biteship Rates */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <div className="flex items-center justify-between border-b border-white/10 pb-2.5">
                <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2">
                  <Truck className="w-4 h-4" /> 2. Pilihan Kurir Pengiriman
                </h3>
                <div className="flex items-center gap-1.5 text-[10px] text-[#CBAC70]">
                  {isLoadingRates ? (
                    <span className="flex items-center gap-1 text-[#CBAC70]">
                      <Loader2 className="w-3 h-3 animate-spin" /> Menghitung Ongkir Biteship...
                    </span>
                  ) : (
                    <span className="bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 px-2 py-0.5 rounded text-[9px] font-bold">
                      ✓ Biteship Logistics Live
                    </span>
                  )}
                </div>
              </div>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                {couriersList.map((courier) => (
                  <div
                    key={courier.id}
                    onClick={() => setSelectedShipping(courier)}
                    className={`p-3 rounded-xl border cursor-pointer transition-all ${
                      selectedShipping.id === courier.id || selectedShipping.name === courier.name
                        ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md'
                        : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8]'
                    }`}
                  >
                    <div className="flex justify-between items-start">
                      <div className="min-w-0 pr-2">
                        <span className="font-bold text-[#FDFCFF] block truncate">{courier.name}</span>
                        <span className="text-[10px] text-[#CBAC70] font-mono">{courier.courier}</span>
                      </div>
                      <span className="font-black text-[#CBAC70] shrink-0">{formatRupiah(courier.cost)}</span>
                    </div>
                    <p className="text-[10px] text-[#94A3B8] mt-1">{courier.etd}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Step 3: Payment Gateway with Live Duitku Channels */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <div className="flex items-center justify-between border-b border-white/10 pb-2.5">
                <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2">
                  <CreditCard className="w-4 h-4" /> 3. Metode Pembayaran
                </h3>
                <span className="bg-[#CBAC70]/10 text-[#CBAC70] border border-[#CBAC70]/30 px-2 py-0.5 rounded text-[9px] font-bold">
                  ⚡ Duitku Official Gateway
                </span>
              </div>

              <div className="space-y-2 text-xs">
                {paymentsList.map((pay) => (
                  <div
                    key={pay.id}
                    onClick={() => setSelectedPayment(pay)}
                    className={`p-3 rounded-xl border cursor-pointer flex items-center justify-between transition-all ${
                      selectedPayment.id === pay.id || selectedPayment.duitkuCode === pay.duitkuCode
                        ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md'
                        : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8]'
                    }`}
                  >
                    <div className="space-y-0.5 min-w-0 pr-2">
                      <div className="flex items-center gap-2">
                        <span className="font-bold text-[#FDFCFF]">{pay.name}</span>
                        {pay.duitkuCode && (
                          <span className="text-[9px] font-mono text-[#CBAC70] bg-[#070D1F] px-1.5 py-0.2 rounded border border-white/10">
                            {pay.duitkuCode}
                          </span>
                        )}
                      </div>
                      <p className="text-[10px] text-[#94A3B8] line-clamp-1">{pay.description}</p>
                    </div>
                    <div className={`w-4 h-4 rounded-full border flex items-center justify-center shrink-0 ${
                      selectedPayment.id === pay.id || selectedPayment.duitkuCode === pay.duitkuCode
                        ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B]'
                        : 'border-white/30'
                    }`}>
                      {(selectedPayment.id === pay.id || selectedPayment.duitkuCode === pay.duitkuCode) && (
                        <Check className="w-2.5 h-2.5 stroke-[3]" />
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Notes Card */}
          <div className="p-4 rounded-2xl bg-[#080E20] border border-white/10 space-y-1.5">
            <span className="text-xs font-bold text-[#FDFCFF] block">Catatan Pesanan:</span>
            <input
              type="text"
              value={buyerNote}
              onChange={e => setBuyerNote(e.target.value)}
              placeholder="Tinggalkan catatan untuk tim packing..."
              className="w-full bg-[#070D1F] border border-white/15 rounded-xl px-3.5 py-2 text-xs text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
            />
          </div>

        </div>

        {/* Right Column: Order Summary (Double Card) */}
        <div className="lg:col-span-5 space-y-4 sm:space-y-6 sticky top-24">
          
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3.5 text-xs">
              
              <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-2.5 flex items-center justify-between">
                <span className="flex items-center gap-1.5">
                  <span>Rincian Pesanan</span>
                  {isInstantBuyActive && (
                    <span className="bg-amber-400/20 text-amber-300 border border-amber-400/40 text-[9px] px-1.5 py-0.5 rounded font-bold uppercase tracking-wider">
                      ⚡ Instant Buy
                    </span>
                  )}
                </span>
                <span>{checkoutItems.length} Produk ({checkoutCount} pcs)</span>
              </h3>

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
                <span className="font-bold text-[#FDFCFF] block">Kode Promo / VIP Voucher:</span>
                <form onSubmit={handleApplyPromo} className="flex gap-1.5">
                  <input
                    type="text"
                    value={voucherInput}
                    onChange={e => setVoucherInput(e.target.value)}
                    placeholder="MALEGAVIP15"
                    className="flex-1 bg-[#0B132B] border border-white/20 rounded-xl px-3 py-1.5 text-xs uppercase font-mono text-[#FDFCFF] placeholder-[#94A3B8] focus:outline-none focus:border-[#CBAC70]"
                  />
                  <button
                    type="submit"
                    className="px-3 py-1.5 bg-[#14204A] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] font-bold text-xs rounded-xl border border-[#CBAC70]/40 transition-colors cursor-pointer"
                  >
                    Terapkan
                  </button>
                </form>

                {voucherError && <p className="text-red-400 text-[10px]">{voucherError}</p>}
                {voucherSuccess && <p className="text-emerald-400 text-[10px]">{voucherSuccess}</p>}

                {/* Applied Vouchers Chips */}
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
                    <Loader2 className="w-4 h-4 animate-spin" /> Menghubungkan ke Duitku Gateway...
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

      {/* Simulated / Fallback Payment Modal with Live Data */}
      {showPaymentModal && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 text-[#FDFCFF]">
            
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div>
                <span className="text-[9px] font-mono text-[#CBAC70] uppercase tracking-widest block font-bold">DUITKU PAYMENT GATEWAY</span>
                <h3 className="font-bold text-sm text-[#FDFCFF]">{selectedPayment.name}</h3>
              </div>
              <button onClick={() => setShowPaymentModal(false)} className="text-[#94A3B8] hover:text-white cursor-pointer">✕</button>
            </div>

            {livePaymentResult?.payment_url && (
              <div className="p-3 bg-[#070D1F] border border-[#CBAC70]/30 rounded-xl space-y-2 text-center">
                <p className="text-xs text-[#94A3B8]">Invoice Duitku Sandbox resmi telah terbit:</p>
                <a
                  href={livePaymentResult.payment_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center gap-1.5 px-4 py-2 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-xl hover:bg-[#E3CD99]"
                >
                  <span>Buka Halaman Pembayaran Duitku</span>
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
                <p className="text-xs text-[#94A3B8]">Transfer ke nomor Virtual Account Duitku berikut:</p>
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
                    ? 'Pesanan akan dikemas & dikirim via Biteship. Mohon siapkan pembayaran tunai saat kurir tiba di alamat tujuan.'
                    : 'Transaksi terproteksi dengan enkripsi perbankan 3D Secure Duitku.'}
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
