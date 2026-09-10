'use client';

import React from 'react';
import Link from 'next/link';
import {
  Truck,
  Package,
  Clock,
  Copy,
  Check,
  ExternalLink,
  RefreshCw,
  ShoppingBag,
  CreditCard,
  MapPin,
  MessageSquare,
  AlertCircle,
  ArrowRight,
} from 'lucide-react';
import { CustomerPastOrder, LiveTrackingOrder, TrackingMilestone } from '../../../types';
import TimelineTab from '../../track/_components/TimelineTab';
import PackageTab from '../../track/_components/PackageTab';
import InvoiceTab from '../../track/_components/InvoiceTab';
import LuxuryToast from '../../track/_components/LuxuryToast';

interface AccountTrackingProps {
  orders: CustomerPastOrder[];
  selectedTrackingOrderNumber: string | null;
  onSelectOrder: (orderNumber: string) => void;
  trackingOrder: LiveTrackingOrder | null;
  isLoadingTracking: boolean;
  trackingError: string | null;
  activeSubTab: 'timeline' | 'package' | 'invoice' | 'address';
  setActiveSubTab: (tab: 'timeline' | 'package' | 'invoice' | 'address') => void;
  copiedKey: string | null;
  isGeneratingInvoice: boolean;
  toast: { title: string; subtitle?: string } | null;
  progressStep: number;
  milestones: TrackingMilestone[];
  courierCompany: string;
  waText: string;
  onCopy: (text: string, key: string, label?: string) => void;
  onPayNow: (orderNumber: string) => void;
  onRefresh: (orderNumber: string) => void;
}

export default function AccountTracking({
  orders,
  selectedTrackingOrderNumber,
  onSelectOrder,
  trackingOrder,
  isLoadingTracking,
  trackingError,
  activeSubTab,
  setActiveSubTab,
  copiedKey,
  isGeneratingInvoice,
  toast,
  progressStep,
  milestones,
  courierCompany,
  waText,
  onCopy,
  onPayNow,
  onRefresh,
}: AccountTrackingProps) {
  const steps = [
    { step: 1, label: 'Dipesan' },
    { step: 2, label: 'Diproses' },
    { step: 3, label: 'Siap Kirim' },
    { step: 4, label: 'Dikirim' },
    { step: 5, label: 'Terkirim' },
  ];

  const stepDescriptions: Record<number, string> = {
    1: 'Tahap 1: Pesanan baru saja dibuat dan menunggu verifikasi pembayaran.',
    2: 'Tahap 2: Busana sedang dipacking dan melewati Quality Control Atelier Malega.',
    3: 'Tahap 3: Busana selesai dikemas rapi dan siap diserahkan kepada kurir ekspedisi.',
    4: 'Tahap 4: Paket sedang dalam perjalanan antar-hub logistik menuju alamat Anda.',
    5: 'Tahap 5: Paket telah sampai dengan aman dan diterima oleh pelanggan.',
  };

  // 1. Empty State if user has zero orders
  if (orders.length === 0) {
    return (
      <div className="py-16 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-4 shadow-xl">
        <div className="w-16 h-16 rounded-2xl bg-[#CBAC70]/10 border border-[#CBAC70]/20 flex items-center justify-center mx-auto text-[#CBAC70]">
          <Truck className="w-8 h-8" />
        </div>
        <div className="space-y-1">
          <p className="text-base font-bold text-slate-200">Belum Ada Pengiriman Aktif</p>
          <p className="text-xs text-slate-400 max-w-md mx-auto">
            Anda belum memiliki pesanan untuk dilacak. Setiap pesanan baru yang Anda buat akan langsung terlacak secara real-time di portal ini.
          </p>
        </div>
        <Link
          href="/katalog"
          className="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#CBAC70] hover:bg-[#E3CD99] text-[#0B132B] font-bold text-xs shadow-lg transition"
        >
          <span>Jelajahi Koleksi</span>
          <ArrowRight className="w-4 h-4" />
        </Link>
      </div>
    );
  }

  return (
    <div className="space-y-6 animate-fade-in">
      {/* 2. Multi-Order Quick Selector Carousel / Pills */}
      <div className="space-y-2.5">
        <div className="flex items-center justify-between">
          <h3 className="text-xs font-mono uppercase font-bold text-[#CBAC70] tracking-wider flex items-center gap-2">
            <Package className="w-3.5 h-3.5" />
            <span>Pilih Pesanan untuk Dilacak ({orders.length})</span>
          </h3>
          {selectedTrackingOrderNumber && (
            <button
              type="button"
              onClick={() => onRefresh(selectedTrackingOrderNumber)}
              disabled={isLoadingTracking}
              className="px-2.5 py-1 rounded-lg bg-white/5 hover:bg-white/10 text-[11px] font-mono font-medium text-slate-300 hover:text-white transition flex items-center gap-1.5 cursor-pointer disabled:opacity-50"
              title="Segarkan status pelacakan logistik"
            >
              <RefreshCw className={`w-3 h-3 ${isLoadingTracking ? 'animate-spin text-[#CBAC70]' : ''}`} />
              <span>{isLoadingTracking ? 'Sinkronisasi...' : 'Segarkan Status'}</span>
            </button>
          )}
        </div>

        {/* Horizontal Order Picker */}
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
          {orders.map((ord) => {
            const isSelected = selectedTrackingOrderNumber === ord.order_number;
            const isCompleted = ['delivered', 'completed'].includes(ord.status);
            const isShipped = ord.status === 'shipped';

            return (
              <button
                key={ord.id}
                type="button"
                onClick={() => onSelectOrder(ord.order_number)}
                className={`text-left p-3.5 rounded-2xl border transition-all cursor-pointer relative overflow-hidden flex flex-col justify-between gap-2 ${
                  isSelected
                    ? 'bg-[#14204A] border-[#CBAC70] shadow-[0_0_15px_rgba(203,172,112,0.25)] ring-1 ring-[#CBAC70]'
                    : 'bg-[#0E1736] border-white/10 hover:border-white/20 hover:bg-[#111C42]'
                }`}
              >
                {isSelected && (
                  <div className="absolute top-0 right-0 w-12 h-12 overflow-hidden pointer-events-none">
                    <div className="bg-[#CBAC70] text-[#0B132B] text-[9px] font-bold py-0.5 text-center transform rotate-45 translate-x-3 -translate-y-1 shadow-sm">
                      AKTIF
                    </div>
                  </div>
                )}

                <div className="space-y-0.5 pr-8">
                  <span className="font-mono font-bold text-xs text-white">
                    {ord.order_number}
                  </span>
                  <p className="text-[10px] text-slate-400 font-mono">
                    {ord.created_at} &bull; {ord.items.length} Item
                  </p>
                </div>

                <div className="flex items-center justify-between pt-1 border-t border-white/5">
                  <span
                    className={`px-2 py-0.5 rounded-full text-[10px] font-mono font-bold ${
                      isCompleted
                        ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30'
                        : isShipped
                        ? 'bg-blue-500/15 text-blue-400 border border-blue-500/30 animate-pulse'
                        : 'bg-amber-500/15 text-amber-400 border border-amber-500/30'
                    }`}
                  >
                    {ord.status_label || ord.status.toUpperCase()}
                  </span>

                  <span className="font-mono font-bold text-xs text-[#CBAC70]">
                    {ord.formatted_total}
                  </span>
                </div>
              </button>
            );
          })}
        </div>
      </div>

      {/* 3. Error Banner */}
      {trackingError && (
        <div className="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs text-center flex items-center justify-center gap-2">
          <AlertCircle className="w-4 h-4 shrink-0" />
          <span>{trackingError}</span>
        </div>
      )}

      {/* 4. Loading Skeleton */}
      {isLoadingTracking && !trackingOrder && (
        <div className="py-16 text-center rounded-3xl bg-[#0E1736] border border-white/5 p-8 space-y-3">
          <RefreshCw className="w-8 h-8 text-[#CBAC70] animate-spin mx-auto" />
          <p className="text-xs font-mono text-slate-400">Menghubungkan ke sistem logistik real-time...</p>
        </div>
      )}

      {/* 5. Live Tracking Order Details */}
      {trackingOrder && (
        <div className="space-y-6">
          {/* Main Hero Status Card */}
          <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 shadow-2xl relative overflow-hidden space-y-5">
            <div className="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-80" />

            {/* Header: Order Number & Live Badges */}
            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
              <div className="space-y-1">
                <div className="flex items-center gap-2">
                  <span className="text-xs font-mono uppercase tracking-wider text-slate-400">
                    Nomor Pesanan
                  </span>
                  <button
                    type="button"
                    onClick={() => onCopy(trackingOrder.orderNumber, 'order', 'Nomor Pesanan')}
                    className="p-1 rounded-md hover:bg-white/10 text-slate-400 hover:text-[#CBAC70] transition-colors cursor-pointer"
                    title="Salin Nomor Pesanan"
                  >
                    {copiedKey === 'order' ? (
                      <span className="text-[10px] text-emerald-400 font-mono font-bold">✓ Tersalin</span>
                    ) : (
                      <Copy className="w-3.5 h-3.5" />
                    )}
                  </button>
                </div>
                <p className="font-mono font-bold text-xl sm:text-2xl text-[#CBAC70] tracking-tight">
                  {trackingOrder.orderNumber}
                </p>
              </div>

              <div className="flex flex-wrap items-center gap-2">
                <span className="px-3 py-1 rounded-full text-xs font-bold bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30">
                  {trackingOrder.orderStatus.label}
                </span>
                <span
                  className={`px-3 py-1 rounded-full text-xs font-bold border ${
                    trackingOrder.paymentStatus.code === 'paid'
                      ? 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30'
                      : 'bg-amber-500/15 text-amber-400 border-amber-500/30 animate-pulse'
                  }`}
                >
                  {trackingOrder.paymentStatus.label}
                </span>
              </div>
            </div>

            {/* Logistics Summary Row */}
            <div className="pt-4 border-t border-white/5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 text-xs text-slate-300">
              <div className="flex items-center gap-2">
                <Truck className="w-4 h-4 text-[#CBAC70] shrink-0" />
                <span>
                  Ekspedisi: <strong className="text-white">{courierCompany}</strong>
                </span>
              </div>

              {trackingOrder.shippingAddress?.tracking_number || trackingOrder.shipment?.waybill_id ? (
                <div className="flex items-center gap-2">
                  <span className="text-slate-400 font-mono">No. Resi:</span>
                  <span className="font-mono font-bold text-slate-100">
                    {trackingOrder.shippingAddress?.tracking_number || trackingOrder.shipment?.waybill_id}
                  </span>
                  <button
                    type="button"
                    onClick={() =>
                      onCopy(
                        trackingOrder.shippingAddress?.tracking_number ||
                          trackingOrder.shipment?.waybill_id ||
                          '',
                        'waybill',
                        'Nomor Resi'
                      )
                    }
                    className="p-1 rounded hover:bg-white/10 text-slate-400 hover:text-[#CBAC70] transition cursor-pointer"
                    title="Salin Nomor Resi"
                  >
                    {copiedKey === 'waybill' ? (
                      <span className="text-[10px] text-emerald-400 font-bold">✓</span>
                    ) : (
                      <Copy className="w-3.5 h-3.5" />
                    )}
                  </button>
                </div>
              ) : (
                <div className="flex items-center gap-2 text-slate-400">
                  <span>Resi: Sedang diproses gudang</span>
                </div>
              )}

              <div className="flex items-center gap-2 text-slate-400">
                <Clock className="w-3.5 h-3.5 shrink-0" />
                <span>
                  {new Date(trackingOrder.createdAt).toLocaleString('id-ID', {
                    day: 'numeric',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit',
                  })}{' '}
                  WIB
                </span>
              </div>
            </div>

            {/* Visual 5-Step Milestone Stepper */}
            <div className="pt-4 border-t border-white/5 space-y-3">
              <div className="relative">
                <div className="absolute top-4 -translate-y-1/2 left-4 right-4 sm:left-6 sm:right-6 h-1 bg-[#14204A] rounded-full overflow-hidden">
                  <div
                    className="h-full bg-gradient-to-r from-[#CBAC70] to-[#E3CD99] transition-all duration-700"
                    style={{ width: `${((progressStep - 1) / 4) * 100}%` }}
                  />
                </div>

                <div className="relative flex justify-between">
                  {steps.map((st) => (
                    <div key={st.step} className="flex flex-col items-center gap-2">
                      <div
                        className={`w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all relative z-10 ring-4 ring-[#0B132B] ${
                          progressStep >= st.step
                            ? 'bg-[#CBAC70] text-[#060913] shadow-[0_0_14px_rgba(203,172,112,0.7)]'
                            : 'bg-[#0B132B] text-slate-500 border border-slate-700'
                        }`}
                      >
                        {progressStep > st.step ? (
                          <Check className="w-3.5 h-3.5 stroke-[3]" />
                        ) : (
                          st.step
                        )}
                      </div>
                      <span
                        className={`text-[10px] sm:text-xs font-medium text-center ${
                          progressStep >= st.step ? 'text-white font-semibold' : 'text-slate-500'
                        }`}
                      >
                        {st.label}
                      </span>
                    </div>
                  ))}
                </div>
              </div>

              {/* Subtitle Step Indicator */}
              <div className="text-center pt-1">
                <p className="text-xs text-[#CBAC70] font-medium">
                  {stepDescriptions[progressStep] || ''}
                </p>
              </div>
            </div>

            {/* Unpaid Pending Alert Card */}
            {trackingOrder.paymentStatus?.code === 'unpaid' && (
              <div className="p-4 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <div className="space-y-0.5">
                  <div className="flex items-center gap-2 text-amber-400 font-bold text-xs sm:text-sm">
                    <span className="w-2 h-2 rounded-full bg-amber-400 animate-ping" />
                    <span>Menunggu Pembayaran</span>
                  </div>
                  <p className="text-xs text-slate-300">
                    Total Tagihan:{' '}
                    <strong className="font-mono text-[#CBAC70]">
                      {trackingOrder.pricing.formatted_grand_total}
                    </strong>
                  </p>
                </div>
                <button
                  type="button"
                  onClick={() =>
                    trackingOrder.payment?.payment_url
                      ? (window.location.href = trackingOrder.payment.payment_url)
                      : onPayNow(trackingOrder.orderNumber)
                  }
                  disabled={isGeneratingInvoice}
                  className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#E3CD99] via-[#CBAC70] to-[#A58645] hover:opacity-95 text-[#0B132B] font-bold text-xs shadow-lg shadow-[#CBAC70]/20 flex items-center justify-center gap-2 active:scale-95 transition-all cursor-pointer"
                >
                  <span>
                    {isGeneratingInvoice ? 'Memuat Gateway...' : '⚡ Bayar Sekarang (Duitku)'}
                  </span>
                  <ExternalLink className="w-3.5 h-3.5" />
                </button>
              </div>
            )}
          </div>

          {/* Sub-Tab Navigation Bar */}
          <div className="flex border-b border-white/10 overflow-x-auto no-scrollbar gap-2 sm:gap-4 text-xs font-mono">
            <button
              type="button"
              onClick={() => setActiveSubTab('timeline')}
              className={`pb-3 px-3 flex items-center gap-2 transition cursor-pointer border-b-2 font-bold whitespace-nowrap ${
                activeSubTab === 'timeline'
                  ? 'border-[#CBAC70] text-[#CBAC70]'
                  : 'border-transparent text-slate-400 hover:text-slate-200'
              }`}
            >
              <Clock className="w-3.5 h-3.5" />
              <span>Linimasa Perjalanan</span>
            </button>

            <button
              type="button"
              onClick={() => setActiveSubTab('package')}
              className={`pb-3 px-3 flex items-center gap-2 transition cursor-pointer border-b-2 font-bold whitespace-nowrap ${
                activeSubTab === 'package'
                  ? 'border-[#CBAC70] text-[#CBAC70]'
                  : 'border-transparent text-slate-400 hover:text-slate-200'
              }`}
            >
              <ShoppingBag className="w-3.5 h-3.5" />
              <span>Daftar Busana ({trackingOrder.items.length})</span>
            </button>

            <button
              type="button"
              onClick={() => setActiveSubTab('invoice')}
              className={`pb-3 px-3 flex items-center gap-2 transition cursor-pointer border-b-2 font-bold whitespace-nowrap ${
                activeSubTab === 'invoice'
                  ? 'border-[#CBAC70] text-[#CBAC70]'
                  : 'border-transparent text-slate-400 hover:text-slate-200'
              }`}
            >
              <CreditCard className="w-3.5 h-3.5" />
              <span>Faktur & Pembayaran</span>
            </button>

            <button
              type="button"
              onClick={() => setActiveSubTab('address')}
              className={`pb-3 px-3 flex items-center gap-2 transition cursor-pointer border-b-2 font-bold whitespace-nowrap ${
                activeSubTab === 'address'
                  ? 'border-[#CBAC70] text-[#CBAC70]'
                  : 'border-transparent text-slate-400 hover:text-slate-200'
              }`}
            >
              <MapPin className="w-3.5 h-3.5" />
              <span>Alamat Tujuan</span>
            </button>
          </div>

          {/* Sub-Tab Contents */}
          <div className="space-y-4">
            {activeSubTab === 'timeline' && (
              <TimelineTab
                order={trackingOrder}
                courierCompany={courierCompany}
                milestones={milestones}
              />
            )}

            {activeSubTab === 'package' && <PackageTab order={trackingOrder} />}

            {activeSubTab === 'invoice' && (
              <InvoiceTab order={trackingOrder} courierCompany={courierCompany} />
            )}

            {activeSubTab === 'address' && (
              <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 space-y-4 shadow-2xl">
                <div className="flex items-center gap-2 pb-3 border-b border-white/10">
                  <MapPin className="w-4 h-4 text-[#CBAC70]" />
                  <p className="font-display font-bold text-base text-white">
                    Detail Alamat Penerima Pesanan
                  </p>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                  <div className="p-4 rounded-2xl bg-[#060913] border border-white/5 space-y-1.5">
                    <span className="text-slate-400 text-[11px] font-mono uppercase">Nama Penerima</span>
                    <p className="font-bold text-white text-sm">
                      {trackingOrder.shippingAddress.recipient_name || '-'}
                    </p>
                    <p className="font-mono text-slate-300">
                      Telp / WA: {trackingOrder.shippingAddress.phone || '-'}
                    </p>
                  </div>

                  <div className="p-4 rounded-2xl bg-[#060913] border border-white/5 space-y-1.5">
                    <span className="text-slate-400 text-[11px] font-mono uppercase">Wilayah Pengiriman</span>
                    <p className="font-semibold text-slate-200">
                      {trackingOrder.shippingAddress.city}, {trackingOrder.shippingAddress.province}
                    </p>
                    <p className="font-mono text-slate-400">
                      Kode Pos: {trackingOrder.shippingAddress.postal_code || '-'}
                    </p>
                  </div>

                  <div className="sm:col-span-2 p-4 rounded-2xl bg-[#060913] border border-white/5 space-y-1.5">
                    <span className="text-slate-400 text-[11px] font-mono uppercase">Alamat Lengkap</span>
                    <p className="text-slate-200 leading-relaxed">
                      {trackingOrder.shippingAddress.address_line1}
                      {trackingOrder.shippingAddress.address_line2
                        ? `, ${trackingOrder.shippingAddress.address_line2}`
                        : ''}
                    </p>
                  </div>
                </div>
              </div>
            )}
          </div>

          {/* WhatsApp Priority Concierge Assistance CTA */}
          <div className="p-5 rounded-3xl bg-gradient-to-br from-[#0E1736] to-[#0B132B] border border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xl">
            <div className="space-y-1 text-center sm:text-left">
              <p className="text-sm font-bold text-white flex items-center justify-center sm:justify-start gap-2">
                <MessageSquare className="w-4 h-4 text-[#CBAC70]" />
                <span>Butuh Bantuan Pelacakan Pesanan Ini?</span>
              </p>
              <p className="text-xs text-slate-400">
                Hubungi Malega Concierge di WhatsApp untuk bantuan pengiriman & jadwal kurir prioritas.
              </p>
            </div>

            <a
              href={`https://wa.me/6281234567890?text=${waText}`}
              target="_blank"
              rel="noopener noreferrer"
              className="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs shadow-lg flex items-center justify-center gap-2 transition active:scale-95"
            >
              <MessageSquare className="w-3.5 h-3.5" />
              <span>Chat WhatsApp Concierge</span>
            </a>
          </div>
        </div>
      )}

      {/* Toast Feedback */}
      <LuxuryToast toast={toast} />
    </div>
  );
}
