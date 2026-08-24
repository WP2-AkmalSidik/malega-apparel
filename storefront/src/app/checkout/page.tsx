'use client';

import React, { useState } from 'react';
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
  Sparkles
} from 'lucide-react';
import { useCart } from '../../context/CartContext';
import { shippingCouriers, paymentGateways } from '../../data/products';

export default function CheckoutPage() {
  const router = useRouter();
  const {
    cart,
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
    createOrder
  } = useCart();

  const [isEditingAddress, setIsEditingAddress] = useState(false);
  const [addressForm, setAddressForm] = useState(selectedAddress);
  const [voucherInput, setVoucherInput] = useState('');
  const [voucherError, setVoucherError] = useState('');
  const [voucherSuccess, setVoucherSuccess] = useState('');
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [isProcessing, setIsProcessing] = useState(false);

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

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

  const handleProceedPayment = () => {
    setIsProcessing(true);
    setTimeout(() => {
      setIsProcessing(false);
      setShowPaymentModal(true);
    }, 500);
  };

  const handleConfirmOrderFinal = () => {
    setShowPaymentModal(false);
    createOrder();
    router.push('/order-confirmation');
  };

  if (cart.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-16 text-center space-y-4">
        <div className="w-16 h-16 rounded-2xl bg-[#14204A] border border-[#CBAC70]/30 text-[#CBAC70] flex items-center justify-center mx-auto">
          <ShoppingBag className="w-8 h-8" />
        </div>
        <h2 className="text-lg font-bold text-[#FDFCFF]">Shopping Bag Kosong</h2>
        <p className="text-xs text-[#94A3B8]">Silakan pilih artikel apparel favorit Anda terlebih dahulu.</p>
        <Link
          href="/"
          className="inline-block px-6 py-3 bg-[#CBAC70] text-[#0B132B] rounded-xl font-black text-xs uppercase tracking-widest hover:bg-[#E3CD99] shadow"
        >
          Lihat Katalog
        </Link>
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
        <span className="text-[11px] text-[#CBAC70] font-mono hidden sm:inline">
          Encrypted 256-Bit SSL
        </span>
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
                  onClick={() => setIsEditingAddress(!isEditingAddress)}
                  className="text-xs text-[#CBAC70] font-bold hover:underline"
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
                    <label className="block text-[#94A3B8] mb-1 font-semibold">Alamat Lengkap</label>
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
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kota/Kab</label>
                      <input
                        type="text"
                        value={addressForm.city}
                        onChange={e => setAddressForm({ ...addressForm, city: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                    <div>
                      <label className="block text-[#94A3B8] mb-1 font-semibold">Kode Pos</label>
                      <input
                        type="text"
                        value={addressForm.postalCode}
                        onChange={e => setAddressForm({ ...addressForm, postalCode: e.target.value })}
                        className="w-full bg-[#0B132B] border border-white/15 rounded-xl p-2 text-[#FDFCFF] focus:outline-none focus:border-[#CBAC70]"
                        required
                      />
                    </div>
                  </div>

                  <button
                    type="submit"
                    className="px-4 py-2 bg-[#CBAC70] text-[#0B132B] font-bold text-xs rounded-xl hover:bg-[#E3CD99]"
                  >
                    Simpan Alamat
                  </button>
                </form>
              ) : (
                <div className="text-xs space-y-1 bg-[#0B132B] p-3.5 rounded-xl border border-white/5">
                  <div className="flex items-center gap-2">
                    <span className="font-bold text-[#FDFCFF]">{selectedAddress.name}</span>
                    <span className="text-[#94A3B8]">({selectedAddress.phone})</span>
                    <span className="bg-[#CBAC70]/20 text-[#CBAC70] text-[9px] font-bold px-2 py-0.2 rounded">
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

          {/* Step 2: Courier Selection with Double Card */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2 border-b border-white/10 pb-2.5">
                <Truck className="w-4 h-4" /> 2. Pilihan Kurir Pengiriman
              </h3>

              <div className="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-xs">
                {shippingCouriers.map((courier) => (
                  <div
                    key={courier.id}
                    onClick={() => setSelectedShipping(courier)}
                    className={`p-3 rounded-xl border cursor-pointer transition-all ${
                      selectedShipping.id === courier.id
                        ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md'
                        : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8]'
                    }`}
                  >
                    <div className="flex justify-between items-start">
                      <span className="font-bold text-[#FDFCFF]">{courier.name}</span>
                      <span className="font-black text-[#CBAC70]">{formatRupiah(courier.cost)}</span>
                    </div>
                    <p className="text-[10px] text-[#94A3B8] mt-0.5">Estimasi tiba: {courier.etd}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Step 3: Payment Gateway with Double Card */}
          <div className="rounded-2xl sm:rounded-3xl bg-gradient-to-b from-[#14204A] via-[#0E1736] to-[#0A1024] p-2 sm:p-2.5 border border-[#CBAC70]/30 shadow-xl">
            <div className="rounded-xl sm:rounded-2xl bg-[#070D1F] border border-white/10 p-4 sm:p-5 space-y-3">
              <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] flex items-center gap-2 border-b border-white/10 pb-2.5">
                <CreditCard className="w-4 h-4" /> 3. Metode Pembayaran
              </h3>

              <div className="space-y-2 text-xs">
                {paymentGateways.map((pay) => (
                  <div
                    key={pay.id}
                    onClick={() => setSelectedPayment(pay)}
                    className={`p-3 rounded-xl border cursor-pointer flex items-center justify-between transition-all ${
                      selectedPayment.id === pay.id
                        ? 'bg-[#14204A] border-[#CBAC70] ring-1 ring-[#CBAC70] shadow-md'
                        : 'bg-[#0B132B] border-white/10 hover:border-white/30 text-[#94A3B8]'
                    }`}
                  >
                    <div className="space-y-0.5 min-w-0 pr-2">
                      <span className="font-bold text-[#FDFCFF] block">{pay.name}</span>
                      <p className="text-[10px] text-[#94A3B8] line-clamp-1">{pay.description}</p>
                    </div>
                    <div className={`w-4 h-4 rounded-full border flex items-center justify-center shrink-0 ${
                      selectedPayment.id === pay.id ? 'border-[#CBAC70] bg-[#CBAC70] text-[#0B132B]' : 'border-white/30'
                    }`}>
                      {selectedPayment.id === pay.id && <Check className="w-2.5 h-2.5 stroke-[3]" />}
                    </div>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Buyer Note */}
          <div className="p-3.5 rounded-xl bg-[#0E1736] border border-white/10 space-y-1.5 text-xs">
            <label className="font-bold text-[#FDFCFF] block">Catatan Pesanan:</label>
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
                <span>Rincian Pesanan</span>
                <span>{cart.length} Item</span>
              </h3>

              <div className="divide-y divide-white/5 max-h-56 overflow-y-auto space-y-2 pr-1">
                {cart.map((item) => (
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
                    className="px-3 py-1.5 bg-[#14204A] hover:bg-[#CBAC70] text-[#CBAC70] hover:text-[#0B132B] font-bold text-xs rounded-xl border border-[#CBAC70]/40 transition-colors"
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
                      <button onClick={() => toggleVoucher(v.code)} className="hover:text-white ml-0.5">✕</button>
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
                  <span>Biaya Pengiriman</span>
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
                onClick={handleProceedPayment}
                disabled={isProcessing}
                className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl shadow-xl flex items-center justify-center gap-2 transition-all active:scale-95 disabled:opacity-50"
              >
                {isProcessing ? (
                  <span>Memverifikasi...</span>
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

      {/* Simulated Payment Modal */}
      {showPaymentModal && (
        <div className="fixed inset-0 z-50 bg-black/85 backdrop-blur-md flex items-center justify-center p-4">
          <div className="bg-[#111D42] border border-[#CBAC70]/40 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-5 animate-in zoom-in-95 text-[#FDFCFF]">
            
            <div className="flex items-center justify-between border-b border-white/10 pb-3">
              <div>
                <span className="text-[9px] font-mono text-[#CBAC70] uppercase tracking-widest block font-bold">PAYMENT GATEWAY</span>
                <h3 className="font-bold text-sm text-[#FDFCFF]">{selectedPayment.name}</h3>
              </div>
              <button onClick={() => setShowPaymentModal(false)} className="text-[#94A3B8] hover:text-white">✕</button>
            </div>

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
                <p className="text-xs text-[#94A3B8]">Transfer ke nomor Virtual Account berikut:</p>
                <div className="p-3.5 rounded-xl bg-[#0B132B] border border-[#CBAC70]/30 space-y-1.5">
                  <div className="flex justify-between text-xs text-[#94A3B8]">
                    <span>Bank:</span>
                    <span className="font-bold text-white">{selectedPayment.bankName || 'BCA'}</span>
                  </div>
                  <div className="flex justify-between items-center pt-1 border-t border-white/5">
                    <span className="font-mono text-base font-black text-[#CBAC70] tracking-wider">
                      {selectedPayment.accountNumber || '8271081234567890'}
                    </span>
                    <button 
                      onClick={() => { navigator.clipboard.writeText(selectedPayment.accountNumber || '8271081234567890'); alert('Nomor VA disalin!'); }}
                      className="text-xs font-bold text-[#CBAC70] hover:underline flex items-center gap-1"
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
                    ? 'Pesanan akan dikirim. Mohon siapkan pembayaran tunai saat kurir tiba di alamat tujuan.'
                    : 'Transaksi terproteksi dengan enkripsi perbankan 3D Secure.'}
                </p>
                <div className="p-3 rounded-xl bg-[#0B132B] border border-white/5 flex justify-between">
                  <span>Total:</span>
                  <span className="font-black text-[#CBAC70]">{formatRupiah(grandTotal)}</span>
                </div>
              </div>
            )}

            <button
              onClick={handleConfirmOrderFinal}
              className="w-full py-3.5 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] text-[#0B132B] font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:opacity-95"
            >
              Saya Sudah Melakukan Pembayaran
            </button>
          </div>
        </div>
      )}

    </div>
  );
}
