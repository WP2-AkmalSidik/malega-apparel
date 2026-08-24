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
