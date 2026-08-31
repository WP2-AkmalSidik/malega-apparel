'use client';

import React from 'react';
import Link from 'next/link';
import { 
  CheckCircle2, 
  Package, 
  Truck, 
  MapPin, 
  Copy, 
  MessageSquare, 
  ArrowRight, 
  Clock, 
  FileText,
  ShieldCheck
} from 'lucide-react';
import { useCart } from '../../context/CartContext';
import BrandLogo from '../../components/BrandLogo';

export default function OrderConfirmationPage() {
  const { lastOrder } = useCart();

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const copyToClipboard = (text: string) => {
    navigator.clipboard.writeText(text);
    alert(`Nomor resi ${text} berhasil disalin!`);
  };

  // Default fallback data if user navigates directly
  const order = lastOrder || {
    orderId: 'ORD-2026-918234',
    invoiceNumber: 'MLG-INV-2026-918234',
    trackingNumber: 'SPXID09821849102',
    items: [
      {
        id: 'mock-1',
        productId: 'mlg-001',
        slug: 'obsidian-heavyweight-boxy-tee-300gsm',
        title: 'Obsidian Heavyweight Boxy Tee 300GSM',
        color: 'Onyx Black',
        size: 'L',
        price: 229000,
        originalPrice: 289000,
        quantity: 1,
        image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
        selected: true
      }
    ],
    address: {
      name: 'Budi Santoso',
      phone: '0812-3456-7890',
      street: 'Gedung Urban Suites Lt. 4 No. 42B, Jl. Kemang Raya',
      district: 'Mampang Prapatan',
      city: 'Jakarta Selatan',
      province: 'DKI Jakarta',
      postalCode: '12730',
      isDefault: true
    },
    shipping: {
      id: 'spx-express',
      name: 'SPX Express Standard',
      service: 'Reguler Express',
      courier: 'SPX Express',
      cost: 15000,
      etd: '1 - 2 Hari Kerja'
    },
    payment: {
      id: 'qris',
      name: 'QRIS Instant Pay',
      description: 'Lunas',
      category: 'qris'
    },
    subtotal: 229000,
    shippingCost: 15000,
    shippingDiscount: 15000,
    productDiscount: 35000,
    serviceFee: 1000,
    total: 195000,
    createdAt: new Date().toLocaleString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    }),
    status: 'Sedang Dikemas Penjual',
    buyerNote: 'Harap dicek sebelum kirim, terima kasih!'
  };

  const waText = encodeURIComponent(
    `Halo Admin Malega Apparel, saya baru saja melakukan pemesanan di Website Resmi:\n\n*No. Invoice:* ${order.invoiceNumber}\n*No. Resi:* ${order.trackingNumber}\n*Nama Penerima:* ${order.address.name} (${order.address.phone})\n*Total Pembayaran:* ${formatRupiah(order.total)}\n*Metode Pembayaran:* ${order.payment.name}\n\nMohon bantu verifikasi dan proses pengirimannya ya min, terima kasih!`
  );

  return (
    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">
      
      {/* Top Brand Banner & Confirmation Card */}
      <div className="luxury-card rounded-3xl p-8 sm:p-12 text-center space-y-6 shadow-2xl relative overflow-hidden border border-[#CBAC70]/40">
        
        {/* Glow */}
        <div className="absolute top-0 left-1/2 -translate-x-1/2 w-64 h-64 bg-[#CBAC70]/10 blur-[100px] rounded-full pointer-events-none" />

        <div className="w-16 h-16 rounded-2xl bg-[#CBAC70]/20 border border-[#CBAC70] text-[#CBAC70] flex items-center justify-center mx-auto shadow-lg">
          <CheckCircle2 className="w-9 h-9" />
        </div>

        <div className="space-y-2">
          <span className="text-xs font-mono font-bold uppercase tracking-widest text-[#CBAC70]">
            TRANSAKSI RESMI MALEGA APPAREL
          </span>
          <h1 className="text-3xl sm:text-4xl font-black text-[#FDFCFF] uppercase tracking-tight">
            Pesanan Berhasil Dikonfirmasi!
          </h1>
          <p className="text-xs sm:text-sm text-[#94A3B8] max-w-md mx-auto leading-relaxed">
            Terima kasih telah berbelanja. Invoice pesanan dan nomor resi pelacakan Anda telah diterbitkan secara otomatis.
          </p>
        </div>

        {/* Invoice Code & Resi Strip */}
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-lg mx-auto pt-2 text-xs">
          <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 text-left space-y-1">
            <span className="text-[#94A3B8] text-[11px] block">Nomor Invoice:</span>
            <span className="font-mono font-black text-[#FDFCFF] text-sm">{order.invoiceNumber}</span>
          </div>

          <div className="p-4 rounded-xl bg-[#080E20] border border-[#CBAC70]/30 text-left space-y-1">
            <span className="text-[#94A3B8] text-[11px] block">No. Resi Pelacakan ({order.shipping.courier}):</span>
            <div className="flex items-center justify-between">
              <span className="font-mono font-black text-[#CBAC70] text-sm">{order.trackingNumber}</span>
              <button 
                onClick={() => copyToClipboard(order.trackingNumber)}
                className="text-[#94A3B8] hover:text-[#CBAC70] p-1"
                title="Salin Resi"
              >
                <Copy className="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>

      </div>

      {/* Order Status Timeline */}
      <div className="luxury-card rounded-3xl p-8 space-y-6">
        <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3 flex items-center gap-2">
          <Clock className="w-4 h-4" /> Live Tracking Timeline
        </h3>

        <div className="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
          
          <div className="p-4 rounded-xl bg-[#080E20] border border-emerald-500/30 space-y-1">
            <span className="text-emerald-400 font-bold flex items-center gap-1">
              ✓ 1. Pesanan Diterima
            </span>
            <p className="text-[11px] text-[#94A3B8]">{order.createdAt}</p>
          </div>

          <div className="p-4 rounded-xl bg-[#172654] border border-[#CBAC70] shadow space-y-1">
            <span className="text-[#CBAC70] font-black flex items-center gap-1 animate-pulse">
              ● 2. Sedang Dikemas
            </span>
            <p className="text-[11px] text-[#94A3B8]">Atelier Malega Bandung</p>
          </div>

          <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 opacity-60 space-y-1">
            <span className="text-[#94A3B8] font-bold">3. Diserahkan ke Kurir</span>
            <p className="text-[11px] text-[#94A3B8]">{order.shipping.name}</p>
          </div>

          <div className="p-4 rounded-xl bg-[#080E20] border border-white/10 opacity-60 space-y-1">
            <span className="text-[#94A3B8] font-bold">4. Tiba di Alamat</span>
            <p className="text-[11px] text-[#94A3B8]">Estimasi {order.shipping.etd}</p>
          </div>

        </div>
      </div>

      {/* Ordered Items & Summary Grid */}
      <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {/* Items List */}
        <div className="lg:col-span-7 luxury-card rounded-3xl p-6 space-y-4 text-xs">
          <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3 flex items-center gap-2">
            <FileText className="w-4 h-4" /> Rincian Item yang Dipesan
          </h3>

          <div className="divide-y divide-white/5 space-y-2">
            {order.items.map((item, idx) => (
              <div key={idx} className="pt-2.5 flex items-center justify-between gap-3">
                <div className="flex items-center gap-3">
                  <img src={item.image} alt={item.title} className="w-12 h-14 rounded-xl object-cover border border-white/10 shrink-0" />
                  <div>
                    <h4 className="font-bold text-[#FDFCFF]">{item.title}</h4>
                    <p className="text-[11px] text-[#94A3B8]">{item.color} • Size {item.size} (x{item.quantity})</p>
                  </div>
                </div>
                <span className="font-black text-[#CBAC70] text-sm">
                  {formatRupiah(item.price * item.quantity)}
                </span>
              </div>
            ))}
          </div>

          <div className="pt-4 border-t border-white/10 text-[#94A3B8] text-[11px] space-y-1">
            <p><strong className="text-white">Alamat Pengiriman:</strong> {order.address.name} ({order.address.phone}) - {order.address.street}, {order.address.city}, {order.address.postalCode}</p>
            <p><strong className="text-white">Metode Pembayaran:</strong> {order.payment.name}</p>
            <p><strong className="text-white">Layanan Kurir:</strong> {order.shipping.name}</p>
          </div>
        </div>

        {/* Financial Summary & Actions */}
        <div className="lg:col-span-5 space-y-6">
          <div className="luxury-card rounded-3xl p-6 space-y-4 text-xs">
            <h3 className="font-bold text-xs uppercase tracking-widest text-[#CBAC70] border-b border-white/10 pb-3">
              Rincian Pembayaran
            </h3>

            <div className="space-y-2 text-[#94A3B8]">
              <div className="flex justify-between">
                <span>Subtotal Produk</span>
                <span className="text-[#FDFCFF] font-semibold">{formatRupiah(order.subtotal)}</span>
              </div>
              <div className="flex justify-between">
                <span>Ongkos Kirim</span>
                <span className="text-[#FDFCFF] font-semibold">{formatRupiah(order.shippingCost)}</span>
              </div>
              {order.shippingDiscount > 0 && (
                <div className="flex justify-between text-emerald-400">
                  <span>Diskon Ongkir</span>
                  <span>-{formatRupiah(order.shippingDiscount)}</span>
                </div>
              )}
              {order.productDiscount > 0 && (
                <div className="flex justify-between text-[#CBAC70]">
                  <span>Voucher Potongan</span>
                  <span>-{formatRupiah(order.productDiscount)}</span>
                </div>
              )}
              <div className="flex justify-between">
                <span>Biaya Layanan</span>
                <span className="text-[#FDFCFF] font-semibold">{formatRupiah(order.serviceFee)}</span>
              </div>

              <div className="border-t border-white/10 pt-3 flex justify-between items-baseline font-bold">
                <span className="text-[#FDFCFF]">Total Pembayaran:</span>
                <span className="text-2xl font-black text-[#CBAC70] gold-gradient-pure">
                  {formatRupiah(order.total)}
                </span>
              </div>
            </div>

            {/* Live Tracking Portal Button */}
            <Link
              href={`/track?q=${order.invoiceNumber || order.trackingNumber}`}
              className="w-full py-4 bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg shadow-[#CBAC70]/20 transition-all active:scale-98"
            >
              <Truck className="w-4 h-4 text-[#0B132B]" />
              <span>Lacak Pengiriman Paket (Live Tracking)</span>
            </Link>

            {/* WhatsApp Notification Button */}
            <a
              href={`https://wa.me/6281234567890?text=${waText}`}
              target="_blank"
              rel="noopener noreferrer"
              className="w-full py-3.5 bg-emerald-600/90 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 shadow-lg transition-all active:scale-98"
            >
              <MessageSquare className="w-4 h-4" />
              <span>Konfirmasi via WhatsApp Admin</span>
            </a>

            <Link
              href="/katalog"
              className="w-full py-3.5 bg-[#111D42] hover:bg-[#172654] border border-[#CBAC70]/30 text-[#CBAC70] rounded-xl font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2 transition-colors"
            >
              <span>Belanja Koleksi Lainnya</span>
              <ArrowRight className="w-3.5 h-3.5" />
            </Link>
          </div>
        </div>

      </div>

    </div>
  );
}
