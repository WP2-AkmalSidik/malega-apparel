export const mockOrderConfirmation = {
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
  status: 'Sedang Dikemas Penjual',
  buyerNote: 'Harap dicek sebelum kirim, terima kasih!'
};
