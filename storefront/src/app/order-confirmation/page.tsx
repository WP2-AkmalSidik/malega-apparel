'use client';

import React, { Suspense, useEffect, useState } from 'react';
import Link from 'next/link';
import { useSearchParams } from 'next/navigation';
import { 
  CheckCircle2, 
  Package, 
  Truck, 
  MapPin, 
  Copy, 
  Check,
  MessageSquare, 
  ArrowRight, 
  Clock, 
  FileText,
  ShieldCheck,
  Loader2,
  ExternalLink,
  Receipt
} from 'lucide-react';
import { useCart } from '../../context/CartContext';
import { products } from '../../data/products';

function OrderConfirmationContent() {
  const searchParams = useSearchParams();
  const orderNumberParam = searchParams.get('order_number') || searchParams.get('merchantOrderId') || searchParams.get('orderId');
  const { lastOrder } = useCart();

  const [liveOrder, setLiveOrder] = useState<any>(null);
  const [isLoadingOrder, setIsLoadingOrder] = useState<boolean>(false);
  const [toast, setToast] = useState<{ title: string; subtitle?: string } | null>(null);
  const [copiedInvoice, setCopiedInvoice] = useState(false);
  const [copiedResi, setCopiedResi] = useState(false);

  const formatRupiah = (val: number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
  };

  const copyText = (text: string, type: 'invoice' | 'resi') => {
    if (!text || text === '-' || text.includes('Menunggu') || text.includes('Sedang Diproses')) {
      setToast({
        title: 'Nomor Resi Belum Diterbitkan',
        subtitle: 'Paket sedang disiapkan penjual. Gunakan Nomor Invoice untuk melacak status pesanan.'
      });
      setTimeout(() => setToast(null), 3000);
      return;
    }

    navigator.clipboard.writeText(text);

    if (type === 'invoice') {
      setCopiedInvoice(true);
      setTimeout(() => setCopiedInvoice(false), 2000);
      setToast({
        title: 'Nomor Invoice Berhasil Disalin',
        subtitle: text
      });
    } else {
      setCopiedResi(true);
      setTimeout(() => setCopiedResi(false), 2000);
      setToast({
        title: 'Nomor Resi Berhasil Disalin',
        subtitle: text
      });
    }

    setTimeout(() => setToast(null), 2500);
  };

  useEffect(() => {
    const currentOrderParam = orderNumberParam;
    if (!currentOrderParam) return;

    let isMounted = true;
    async function fetchLiveOrder() {
      setIsLoadingOrder(true);
      try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'https://malega.my.id/api/v1';
        const orderCode = String(currentOrderParam);
        const res = await fetch(`${apiUrl}/orders/${encodeURIComponent(orderCode)}`);
        const json = await res.json();
        if (isMounted && json.success && json.data) {
          const data = json.data;
          const actualTrackingNum = data.shipment?.waybill_id || data.shipping_address?.tracking_number || null;
          const invoiceNum = data.order_number || currentOrderParam;

          // Save session to localStorage for seamless auto-tracking
          try {
            localStorage.setItem('malega_last_order', JSON.stringify({
              orderNumber: invoiceNum,
              trackingNumber: actualTrackingNum,
              grandTotal: data.pricing?.grand_total || data.grand_total,
              createdAt: data.created_at || new Date().toISOString()
            }));
            localStorage.setItem('malega_last_order_number', invoiceNum);
            if (actualTrackingNum) {
              localStorage.setItem('malega_last_tracking_number', actualTrackingNum);
            }
          } catch (e) {
            console.warn('Storage write error:', e);
          }

          setLiveOrder({
            orderId: invoiceNum,
            invoiceNumber: invoiceNum,
            trackingNumber: actualTrackingNum || 'Sedang Diproses Penjual',
            hasActualResi: Boolean(actualTrackingNum),
            items: (data.items || []).map((item: any) => {
              const catalogMatch = products.find(p => 
                (p.title && item.product_name && p.title.toLowerCase().includes(item.product_name.toLowerCase())) ||
                (p.title && item.product_name && item.product_name.toLowerCase().includes(p.title.toLowerCase())) ||
                (p.slug && item.sku && item.sku.toLowerCase().includes(p.slug.toLowerCase()))
              );
              const colorParsed = item.variant_title?.split('/')[0]?.trim() || 'Signature';
              const sizeParsed = item.variant_title?.split('/')[1]?.trim() || 'All Size';
              const fallbackImg = catalogMatch?.gallery?.[0] || catalogMatch?.colors?.[0]?.image || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
              return {
                id: item.id || item.sku,
                title: item.product_name,
                color: colorParsed,
                size: sizeParsed,
                price: item.unit_price,
                quantity: item.quantity,
                image: item.image_url || fallbackImg,
              };
            }),
            address: {
              name: data.customer?.name || data.shipping_address?.recipient_name || 'Pelanggan Malega',
              phone: data.customer?.phone || data.shipping_address?.phone || '081234567890',
              street: data.shipping_address?.address_line1 || 'Jl. Kemang Raya',
              city: data.shipping_address?.city || 'Jakarta Selatan',
              postalCode: data.shipping_address?.postal_code || '12730',
            },
            shipping: {
              name: data.shipping_address?.courier_name || data.shipment?.courier || 'Biteship Logistics',
              courier: data.shipment?.courier || data.shipping_address?.courier_name || 'Biteship Express',
              cost: data.pricing?.shipping_total ?? data.shipping_total ?? 15000,
              etd: '1 - 2 Hari Kerja'
            },
            payment: {
              name: data.payment?.payment_method_name || 'Duitku Payment Gateway',
              category: 'duitku',
              status: data.payment_status?.label || 'Lunas',
            },
            subtotal: data.pricing?.subtotal ?? data.subtotal ?? 0,
            shippingCost: data.pricing?.shipping_total ?? data.shipping_total ?? 0,
            shippingDiscount: (data.pricing?.shipping_total && data.pricing?.shipping_total > 0 && data.pricing?.grand_total < (data.pricing?.subtotal + data.pricing?.shipping_total)) ? 15000 : 0,
            productDiscount: data.pricing?.discount_total ?? data.discount_total ?? 0,
            serviceFee: data.pricing?.service_fee ?? 1000,
            total: data.pricing?.grand_total ?? data.grand_total ?? 0,
            createdAt: data.created_at ? new Date(data.created_at).toLocaleString('id-ID', {
              day: 'numeric',
              month: 'short',
              year: 'numeric',
              hour: '2-digit',
              minute: '2-digit'
            }) : new Date().toLocaleString('id-ID'),
            status: data.order_status?.label || 'Sedang Diproses',
          });
        }
      } catch (err) {
        console.warn('Failed to fetch live order tracking confirmation:', err);
      } finally {
        if (isMounted) setIsLoadingOrder(false);
      }
    }

    fetchLiveOrder();

    return () => {
      isMounted = false;
    };
  }, [orderNumberParam]);

  // Order display hierarchy: Live API Order -> Cart Last Order -> Mock Demo Order
  const order = liveOrder || (lastOrder ? {
    ...lastOrder,
    hasActualResi: Boolean(lastOrder.trackingNumber && !lastOrder.trackingNumber.includes('undefined'))
  } : {
    orderId: 'ORD-2026-918234',
    invoiceNumber: 'MLG-INV-2026-918234',
    trackingNumber: 'SPXID09821849102',
    hasActualResi: true,
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
      name: 'QRIS Instant Pay (Duitku)',
      description: 'Lunas',
      category: 'qris',
      status: 'Lunas'
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
  });

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
        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-xl mx-auto pt-2 text-xs">
          
          {/* Invoice Box */}
          <div className="p-4 rounded-2xl bg-[#080E20]/90 border border-[#CBAC70]/30 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-[#CBAC70] transition-all">
            <div className="flex items-center justify-between">
              <span className="text-[#94A3B8] text-[11px] font-medium flex items-center gap-1.5">
                <Receipt className="w-3.5 h-3.5 text-[#CBAC70]" />
                Nomor Invoice:
              </span>
              <span className="text-[10px] text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-1.5 py-0.5 rounded font-medium">
                {order.payment?.status || 'Lunas'}
              </span>
            </div>
            <div className="flex items-center justify-between gap-2">
              <span className="font-mono font-black text-[#FDFCFF] text-sm tracking-tight truncate">
                {order.invoiceNumber}
              </span>
              <div className="flex items-center gap-1">
                <button 
                  onClick={() => copyText(order.invoiceNumber, 'invoice')}
                  className="p-1.5 rounded-lg bg-white/5 hover:bg-[#CBAC70]/20 text-[#94A3B8] hover:text-[#CBAC70] border border-white/10 hover:border-[#CBAC70]/40 transition-all active:scale-90 cursor-pointer"
                  title="Salin Nomor Invoice"
                >
                  {copiedInvoice ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
                </button>
                <Link
                  href={`/track?q=${encodeURIComponent(order.invoiceNumber)}`}
                  className="p-1.5 rounded-lg bg-[#CBAC70]/10 hover:bg-[#CBAC70]/25 text-[#CBAC70] border border-[#CBAC70]/30 transition-all active:scale-90"
                  title="Lacak dengan Invoice"
                >
                  <ExternalLink className="w-3.5 h-3.5" />
                </Link>
              </div>
            </div>
          </div>

          {/* Resi Box */}
          <div className="p-4 rounded-2xl bg-[#080E20]/90 border border-white/10 text-left space-y-1.5 backdrop-blur-md shadow-lg group hover:border-white/25 transition-all">
            <div className="flex items-center justify-between">
              <span className="text-[#94A3B8] text-[11px] font-medium flex items-center gap-1.5">
                <Truck className="w-3.5 h-3.5 text-sky-400" />
                No. Resi ({order.shipping.courier}):
              </span>
              {order.hasActualResi ? (
                <span className="text-[10px] text-sky-400 bg-sky-500/10 border border-sky-500/20 px-1.5 py-0.5 rounded font-medium">
                  Aktif
                </span>
              ) : (
                <span className="text-[10px] text-amber-400 bg-amber-500/10 border border-amber-500/20 px-1.5 py-0.5 rounded font-medium">
                  Proses Kemas
                </span>
              )}
            </div>
            <div className="flex items-center justify-between gap-2">
              <span className={`font-mono text-xs sm:text-sm font-bold truncate ${order.hasActualResi ? 'text-[#CBAC70]' : 'text-slate-400'}`}>
                {order.trackingNumber}
              </span>
              <button 
                onClick={() => copyText(order.trackingNumber, 'resi')}
                className="p-1.5 rounded-lg bg-white/5 hover:bg-[#CBAC70]/20 text-[#94A3B8] hover:text-[#CBAC70] border border-white/10 hover:border-[#CBAC70]/40 transition-all active:scale-90 cursor-pointer"
                title="Salin Nomor Resi"
              >
                {copiedResi ? <Check className="w-3.5 h-3.5 text-emerald-400" /> : <Copy className="w-3.5 h-3.5" />}
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
            {order.items.map((item: any, idx: number) => (
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
              href={`/track?q=${encodeURIComponent(order.invoiceNumber)}`}
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

      {/* Modern Minimalist Luxury Toast Notification */}
      {toast && (
        <div className="fixed bottom-6 inset-x-0 z-50 flex justify-center px-4 pointer-events-none animate-in fade-in slide-in-from-bottom-5 duration-200">
          <div className="pointer-events-auto max-w-sm w-full bg-[#0B132B]/95 backdrop-blur-xl border border-[#CBAC70]/50 rounded-2xl p-3.5 shadow-2xl shadow-black/80 flex items-center gap-3">
            <div className="w-8 h-8 rounded-xl bg-[#CBAC70]/20 border border-[#CBAC70]/40 text-[#CBAC70] flex items-center justify-center shrink-0 shadow-sm">
              <Check className="w-4 h-4 stroke-[2.5]" />
            </div>
            <div className="min-w-0 flex-1 space-y-0.5">
              <p className="text-xs font-bold text-white tracking-tight">{toast.title}</p>
              {toast.subtitle && (
                <p className="text-[11px] font-mono text-[#CBAC70] truncate">{toast.subtitle}</p>
              )}
            </div>
          </div>
        </div>
      )}

    </div>
  );
}

export default function OrderConfirmationPage() {
  return (
    <Suspense fallback={
      <div className="max-w-4xl mx-auto px-4 py-20 text-center space-y-3">
        <Loader2 className="w-8 h-8 animate-spin text-[#CBAC70] mx-auto" />
        <p className="text-xs text-[#94A3B8]">Memuat konfirmasi pesanan...</p>
      </div>
    }>
      <OrderConfirmationContent />
    </Suspense>
  );
}
