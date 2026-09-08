import { LiveTrackingOrder, TrackingMilestone } from '../../../types';
import { formatRupiah } from '../../../lib/utils';

/**
 * Synthesize comprehensive milestone events from an order's tracking data.
 * Produces a reverse-chronological array of milestones for the timeline view.
 */
export function getMilestones(order: LiveTrackingOrder, progressStep: number): TrackingMilestone[] {
  const list: TrackingMilestone[] = [];
  const courier = order.shipment?.courier || order.shippingAddress?.courier_name || 'Kurir Ekspedisi';

  const formatTimestamp = (date: Date) =>
    date.toLocaleString('id-ID', {
      day: 'numeric',
      month: 'short',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit',
    }) + ' WIB';

  // 1. Order Placed
  list.push({
    title: 'Pesanan Berhasil Dibuat',
    note: `Pesanan #${order.orderNumber} telah diterima dan diverifikasi di sistem Malega.`,
    status: 'order_placed',
    timestamp: formatTimestamp(new Date(order.createdAt)),
    location: 'Storefront Malega Apparel',
  });

  // 2. Payment Verified
  if (order.paymentStatus?.code === 'paid' || progressStep >= 2) {
    const payTime = order.payment?.paid_at
      ? formatTimestamp(new Date(order.payment.paid_at))
      : formatTimestamp(new Date(new Date(order.createdAt).getTime() + 120000));

    list.push({
      title: 'Pembayaran Lunas & Terverifikasi',
      note: `Pembayaran sebesar ${order.pricing.formatted_grand_total} berhasil dikonfirmasi (${order.payment?.payment_method_name || 'Payment Gateway'}). Busana siap dikemas.`,
      status: 'payment_verified',
      timestamp: payTime,
      location: 'Duitku Payment Gateway',
    });
  }

  // 3. Ready to ship / packed
  if (order.shipment?.waybill_id || progressStep >= 3) {
    list.push({
      title: `Paket Telah Dikemas & Siap Kirim (${courier})`,
      note: `Busana pesanan Anda telah selesai dikemas rapi dengan kotak eksklusif Malega & segel QC, dan siap diserahterimakan kepada kurir ekspedisi.`,
      status: 'ready_to_ship',
      timestamp: formatTimestamp(new Date(new Date(order.createdAt).getTime() + 900000)),
      location: 'Gudang Pusat Malega (Jakarta Pusat)',
    });
  }

  // 4. Raw Biteship History Events
  const history = order.shipment?.tracking_history || [];
  history.forEach((h) => {
    let tTitle = 'Pembaruan Status Ekspedisi';
    let tLoc = 'Hub Sortir Logistik';
    if (['picking_up', 'allocated'].includes(h.status)) {
      tTitle = `Kurir Ditugaskan (${courier})`;
      tLoc = 'Gudang Malega (Jakarta Pusat)';
    } else if (h.status === 'picked') {
      tTitle = 'Paket Telah Di-Pickup Kurir';
      tLoc = 'Gudang Malega (Jakarta Pusat)';
    } else if (['dropping_off', 'in_transit'].includes(h.status)) {
      tTitle = 'Paket Sedang Dalam Perjalanan';
      tLoc = `Hub Ekspedisi ${order.shippingAddress?.city || 'Transit'}`;
    } else if (h.status === 'delivered') {
      tTitle = 'Paket Berhasil Diterima';
      tLoc = `${order.shippingAddress?.recipient_name} (${order.shippingAddress?.city})`;
    }

    list.push({
      title: tTitle,
      note: h.note,
      status: h.status,
      timestamp: h.updated_at
        ? formatTimestamp(new Date(h.updated_at))
        : '-',
      location: tLoc,
    });
  });

  if (progressStep === 4 && history.length <= 1) {
    list.push({
      title: `Paket Menuju Hub Sortir Tujuan (${courier})`,
      note: 'Paket busana dalam perjalanan menuju fasilitas distribusi kota tujuan penerima.',
      status: 'in_transit',
      timestamp: formatTimestamp(new Date()),
      location: 'Main Logistics Gateway',
    });
  }

  if (progressStep === 5 && !history.some((x) => x.status === 'delivered')) {
    list.push({
      title: 'Paket Berhasil Diterima Pelanggan',
      note: `Paket telah diterima dengan baik oleh ${order.shippingAddress?.recipient_name}.`,
      status: 'delivered',
      timestamp: formatTimestamp(new Date()),
      location: `Alamat Penerima (${order.shippingAddress?.city})`,
    });
  }

  return list
    .map((item, idx) => ({
      ...item,
      isActive: idx === list.length - 1,
    }))
    .reverse();
}
