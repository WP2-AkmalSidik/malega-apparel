import { LiveTrackingOrder } from '../../../types';

/**
 * Fallback/mock order data used when a test identifier (MLG-, WYB-, JNE-)
 * is searched but not found in the database.
 */
export function createFallbackOrder(term: string): LiveTrackingOrder {
  const isDelivered = term.toLowerCase().includes('deliv');

  return {
    orderNumber: term.startsWith('WYB-') ? 'MLG-20260904-2637' : term,
    createdAt: new Date().toISOString(),
    orderStatus: { code: 'processing', label: 'Sedang Diproses' },
    paymentStatus: { code: 'paid', label: 'Lunas' },
    fulfillmentStatus: {
      code: isDelivered ? 'delivered' : 'fulfilled',
      label: isDelivered ? 'Terkirim' : 'Diproses Kurir',
    },
    pricing: {
      subtotal: 189000,
      discount_total: 15000,
      shipping_total: 15000,
      service_fee: 1000,
      tax_total: 0,
      grand_total: 190000,
      formatted_grand_total: 'Rp 190.000',
    },
    customer: {
      name: 'Ak*** R***',
      email: 'ak***@malega.id',
      phone: '0812****899',
    },
    shippingAddress: {
      recipient_name: 'Ak*** R***',
      phone: '0812****899',
      address_line1: 'Jl. Senopati No. 25 **** (Disamarkan demi privasi)',
      address_line2: 'Kebayoran Baru',
      city: 'Jakarta Selatan',
      province: 'DKI Jakarta',
      postal_code: '*****',
      courier_name: 'JNE (REG)',
      tracking_number: 'WYB-1788764838210',
    },
    shipment: {
      courier: 'JNE',
      service: 'REG',
      waybill_id: 'WYB-1788764838210',
      status: isDelivered ? 'delivered' : 'confirmed',
      status_label: isDelivered ? 'Paket Diterima' : 'Menunggu Pickup',
      tracking_url: 'https://track.biteship.com/f6XUSKFG9Et4hSAUtRQ78rsR?environment=development',
      tracking_history: [
        {
          status: 'confirmed',
          note: 'Courier order is confirmed. JNE has been notified to pick up.',
          updated_at: new Date().toISOString(),
        },
      ],
    },
    payment: {
      reference: 'SIMULATED-126E677087B2',
      payment_method: 'SP',
      payment_method_name: 'QRIS Real-Time',
      status: 'success',
      paid_at: new Date().toISOString(),
    },
    items: [
      {
        sku: 'MLG-STRU-BLK-ALL-SIZE-ADJUSTABLE',
        product_name: 'Structured Minimal 6-Panel Gold Monogram Cap',
        variant_title: 'Structured Minimal 6-Panel Gold Monogram Cap - Onyx Black / Gold / All Size (Adjustable)',
        unit_price: 189000,
        formatted_unit_price: 'Rp 189.000',
        quantity: 1,
        subtotal: 189000,
        formatted_subtotal: 'Rp 189.000',
      },
    ],
  };
}
