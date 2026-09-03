export interface ColorOption {
  name: string;
  hex: string;
  image: string;
}

export interface Product {
  id: string;
  slug: string;
  title: string;
  subtitle: string;
  isNewDrop?: boolean;
  isBestSeller?: boolean;
  rating: number;
  reviewCount: number;
  soldCount: number;
  originalPrice: number;
  price: number;
  discountPercentage: number;
  description: string;
  category: 'T-Shirts' | 'Outerwear' | 'Bottoms' | 'Accessories';
  material: string;
  gsm: number;
  fit: string;
  origin: string;
  stockTotal: number;
  colors: ColorOption[];
  sizes: string[];
  gallery: string[];
  features: string[];
  specifications: { [key: string]: string };
}

export interface CartItem {
  id: string;
  productId: string;
  slug: string;
  title: string;
  color: string;
  size: string;
  price: number;
  originalPrice: number;
  quantity: number;
  image: string;
  selected: boolean;
}

export interface Voucher {
  code: string;
  title: string;
  minSpend: number;
  discount: number;
  type: 'fixed' | 'percentage' | 'shipping';
  applied: boolean;
}

export interface ShippingOption {
  id: string;
  name: string;
  service: string;
  courier: string;
  cost: number;
  etd: string;
}

export interface PaymentMethod {
  id: string;
  name: string;
  description: string;
  category: 'qris' | 'va' | 'card' | 'paylater' | 'cod';
  accountNumber?: string;
  bankName?: string;
}

export interface Address {
  name: string;
  phone: string;
  street: string;
  district: string;
  city: string;
  province: string;
  postalCode: string;
  notes?: string;
  isDefault: boolean;
}

export interface OrderReceipt {
  orderId: string;
  invoiceNumber: string;
  trackingNumber: string;
  items: CartItem[];
  address: Address;
  shipping: ShippingOption;
  payment: PaymentMethod;
  subtotal: number;
  shippingCost: number;
  shippingDiscount: number;
  productDiscount: number;
  serviceFee: number;
  total: number;
  createdAt: string;
  status: 'Payment Pending' | 'Sedang Dikemas Penjual' | 'Dalam Pengiriman' | 'Terkirim';
  buyerNote: string;
}

export interface CatalogCollection {
  id: string;
  slug: string;
  title: string;
  subtitle: string;
  season: string;
  releaseYear: string;
  badge: string;
  coverImage: string;
  bannerImage: string;
  totalArticles: number;
  featuredMaterial: string;
  gsmWeight?: number;
  description: string;
  storytelling: string;
  palette: string[];
  tags: string[];
  productIds: string[];
}

export interface TrackingMilestone {
  title: string;
  note: string;
  status: string;
  timestamp: string;
  location?: string;
  isActive?: boolean;
}

export interface LiveTrackingOrder {
  orderNumber: string;
  createdAt: string;
  orderStatus: { code: string; label: string };
  paymentStatus: { code: string; label: string };
  fulfillmentStatus: { code: string; label: string };
  pricing: {
    subtotal: number;
    discount_total: number;
    shipping_total: number;
    tax_total: number;
    grand_total: number;
    formatted_grand_total: string;
  };
  customer: {
    name: string;
    email?: string;
    phone?: string;
  };
  shippingAddress: {
    recipient_name: string;
    phone: string;
    address_line1: string;
    address_line2?: string;
    city: string;
    province: string;
    postal_code: string;
    courier_name?: string;
    tracking_number?: string;
  };
  shipment?: {
    courier: string;
    service: string;
    waybill_id: string;
    status: string;
    status_label: string;
    tracking_url?: string;
    tracking_history?: Array<{ status: string; note: string; updated_at?: string }>;
    shipped_at?: string;
    delivered_at?: string;
  } | null;
  items: Array<{
    sku: string;
    product_name: string;
    variant_title: string;
    unit_price: number;
    formatted_unit_price: string;
    quantity: number;
    subtotal: number;
    formatted_subtotal: string;
  }>;
}

export interface CustomerProfile {
  id: number;
  name: string;
  email: string;
  phone: string;
  membership_tier: 'Silver' | 'Gold' | 'VIP Platinum';
  marketing_opt_in: boolean;
  total_orders: number;
  total_spend: number;
  formatted_spend?: string;
  wishlist: string[];
  saved_addresses: Address[];
}

export interface AuthResponse {
  success: boolean;
  message?: string;
  data?: {
    token: string;
    customer: CustomerProfile;
  };
}

export interface CustomerPastOrder {
  id: number;
  order_number: string;
  status: string;
  status_label: string;
  total_amount: number;
  formatted_total: string;
  created_at: string;
  items: Array<{
    title: string;
    sku: string;
    price: number;
    quantity: number;
    subtotal: number;
  }>;
  shipping?: {
    courier: string;
    waybill: string;
    tracking_url: string;
  } | null;
  payment?: {
    method: string;
    status: string;
  } | null;
}

