import React from 'react';
import { Clock, Truck, MapPin, ExternalLink } from 'lucide-react';
import { LiveTrackingOrder, TrackingMilestone } from '../../../types';

interface TimelineTabProps {
  order: LiveTrackingOrder;
  courierCompany: string;
  milestones: TrackingMilestone[];
}

export default function TimelineTab({ order, courierCompany, milestones }: TimelineTabProps) {
  return (
    <div className="space-y-4">
      {/* Status Pengiriman Ringkas & Informatif */}
      <div className="p-5 rounded-3xl bg-[#0B132B] border border-white/10 space-y-3.5">
        <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div className="space-y-1">
            <div className="flex items-center gap-2">
              <span className="font-bold text-sm text-white">{courierCompany}</span>
              <span className="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-500/15 text-sky-400 border border-sky-500/30">
                {order.shipment?.status_label || 'Sedang Diproses'}
              </span>
            </div>
            <p className="text-xs text-slate-400">
              Tujuan Penerima: <strong className="text-slate-200">{order.shippingAddress.recipient_name}</strong> &bull; {order.shippingAddress.city}
            </p>
          </div>

          {order.shipment?.tracking_url && (
            <a
              href={order.shipment.tracking_url}
              target="_blank"
              rel="noopener noreferrer"
              className="self-start sm:self-auto px-3.5 py-1.5 rounded-xl bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/30 font-semibold text-xs flex items-center gap-1.5 transition-all active:scale-95"
            >
              <span>Portal Ekspedisi</span>
              <ExternalLink className="w-3.5 h-3.5" />
            </a>
          )}
        </div>

        {/* Informative Notice */}
        <div className="p-3.5 rounded-2xl bg-[#060913] border border-white/5 flex items-center gap-3 text-xs text-slate-300">
          <div className="w-8 h-8 rounded-xl bg-[#CBAC70]/10 border border-[#CBAC70]/20 text-[#CBAC70] flex items-center justify-center shrink-0">
            <Truck className="w-4 h-4" />
          </div>
          <p className="text-[11px] text-slate-300 leading-relaxed">
            Pesanan dalam penanganan logistik resmi Malega. Pantau riwayat perjalanan paket Anda secara langsung melalui linimasa di bawah.
          </p>
        </div>
      </div>

      {/* Milestones Vertical Feed */}
      <div className="p-5 sm:p-6 rounded-3xl bg-[#0B132B] border border-white/10 space-y-4">
        <h4 className="font-mono text-xs uppercase font-bold text-[#CBAC70] tracking-wider flex items-center gap-2">
          <Clock className="w-3.5 h-3.5" />
          <span>Riwayat Perjalanan Paket</span>
        </h4>

        <div className="relative pl-6 space-y-4 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-white/10">
          {milestones.map((m, idx) => (
            <div key={idx} className="relative space-y-1">
              {/* Bullet Marker */}
              {m.isActive ? (
                <div className="absolute -left-6 top-1 w-4 h-4 rounded-full bg-sky-500 flex items-center justify-center shadow-[0_0_10px_rgba(56,189,248,1)]">
                  <span className="w-1.5 h-1.5 rounded-full bg-white animate-ping" />
                </div>
              ) : (
                <div className="absolute -left-5 top-1.5 w-2.5 h-2.5 rounded-full bg-slate-700 border border-slate-600" />
              )}

              <div
                className={`p-3.5 rounded-2xl space-y-1 text-xs ${
                  m.isActive ? 'bg-sky-950/20 border border-sky-500/30' : 'bg-[#060913] border border-white/5'
                }`}
              >
                <div className="flex items-center justify-between gap-2">
                  <span className={`font-bold ${m.isActive ? 'text-sky-400' : 'text-white'}`}>
                    {m.title}
                  </span>
                  <span className="text-[10px] font-mono text-slate-400">{m.timestamp}</span>
                </div>
                <p className="text-slate-300 leading-relaxed text-[11px]">{m.note}</p>
                {m.location && (
                  <p className="text-[10px] text-slate-400 flex items-center gap-1 pt-1">
                    <MapPin className="w-3 h-3 text-[#CBAC70]" />
                    <span>{m.location}</span>
                  </p>
                )}
              </div>
            </div>
          ))}
        </div>
      </div>
    </div>
  );
}
