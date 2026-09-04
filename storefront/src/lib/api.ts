import { Product, ProductVariant, CatalogCollection, ShippingOption, PaymentMethod, Voucher } from '../types';
import { productsCatalog, shippingCouriers, paymentGateways, availableVouchers } from '../data/products';
import { katalogCollections } from '../data/katalog';

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api/v1';

export async function fetchCollectionsFromApi(): Promise<CatalogCollection[]> {
  try {
    const res = await fetch(`${API_BASE}/collections`, {
      next: { revalidate: 0 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch collections: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data)) {
      return json.data.map((item: any) => ({
        id: String(item.id),
        slug: item.slug,
        name: item.name,
        title: item.title || item.name,
        subtitle: item.subtitle || '',
        season: item.season || 'Spring / Summer',
        releaseYear: item.release_year || '2026',
        badge: item.badge || '',
        coverImage: item.cover_image || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
        bannerImage: item.banner_image || item.banner_url || 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80',
        totalArticles: item.products_count || 0,
        featuredMaterial: item.featured_material || '',
        gsmWeight: item.gsm_weight,
        description: item.description || '',
        storytelling: item.storytelling || '',
        palette: Array.isArray(item.palette) ? item.palette : [],
        tags: Array.isArray(item.tags) ? item.tags : [],
        productIds: Array.isArray(item.product_ids) ? item.product_ids : [],
      }));
    }
  } catch (err) {
    console.warn('Backend API collections unreachable, falling back to local katalog:', err);
  }

  return katalogCollections;
}

export async function fetchCollectionDetailFromApi(slug: string): Promise<CatalogCollection | null> {
  try {
    const res = await fetch(`${API_BASE}/collections/${slug}`, {
      next: { revalidate: 0 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch collection detail: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && json.data) {
      const item = json.data;
      const products = Array.isArray(item.products) ? item.products.map((p: any) => {
        const defaultImg = p.featured_image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
        const colors = (Array.isArray(p.colors) && p.colors.length > 0)
          ? p.colors
          : [{ name: 'Signature', hex: '#0B132B', image: defaultImg }];
        const sizes = (Array.isArray(p.sizes) && p.sizes.length > 0)
          ? p.sizes
          : ['All Size'];

        return {
          id: String(p.id),
          slug: p.slug,
          title: p.name,
          subtitle: p.subtitle || '',
          badge: p.badge,
          isNewDrop: p.badge?.includes('NEW') || p.badge?.includes('DROP') || Number(p.id) >= 10,
          isBestSeller: p.badge?.includes('BEST') || p.badge?.includes('TOP') || (p.sold_count && p.sold_count > 1000),
          rating: p.rating,
          reviewCount: p.review_count,
          soldCount: p.sold_count,
          originalPrice: p.price?.compare_at || p.price?.max || p.price?.min,
          price: p.price?.min || 0,
          priceMin: p.price?.min,
          priceMax: p.price?.max,
          discountPercentage: p.price?.discount_percentage || 0,
          category: p.category?.name || 'Streetwear',
          material: p.material || p.specifications?.Material || '',
          gsm: p.gsm || (p.specifications?.Gramasi ? parseInt(p.specifications.Gramasi) : 300),
          fit: p.fit || p.specifications?.Cutting || p.specifications?.['Fit / Cutting'] || '',
          origin: 'Bandung, Indonesia',
          stockTotal: p.variants_count * 10,
          colors,
          sizes,
          gallery: [defaultImg],
          features: [],
          specifications: p.specifications || {},
          variants: p.variants || [],
        };
      }) : [];

      return {
        id: String(item.id),
        slug: item.slug,
        name: item.name,
        title: item.title || item.name,
        subtitle: item.subtitle || '',
        season: item.season || 'Spring / Summer',
        releaseYear: item.release_year || '2026',
        badge: item.badge || '',
        coverImage: item.cover_image || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
        bannerImage: item.banner_image || item.banner_url || 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=1200&auto=format&fit=crop&q=80',
        totalArticles: products.length || item.products_count || 0,
        featuredMaterial: item.featured_material || '',
        gsmWeight: item.gsm_weight,
        description: item.description || '',
        storytelling: item.storytelling || '',
        palette: Array.isArray(item.palette) ? item.palette : [],
        tags: Array.isArray(item.tags) ? item.tags : [],
        productIds: Array.isArray(item.product_ids) ? item.product_ids : [],
        products,
      };
    }
  } catch (err) {
    console.warn(`Backend API collection detail failed for ${slug}, using local fallback:`, err);
  }

  return katalogCollections.find(c => c.slug === slug || c.id === slug) || null;
}

export async function fetchProductsFromApi(params?: {
  category?: string;
  collection?: string;
  search?: string;
  sort?: string;
  perPage?: number;
}): Promise<Product[]> {
  try {
    const searchParams = new URLSearchParams();
    if (params?.category && params.category !== 'all') searchParams.set('category', params.category);
    if (params?.collection) searchParams.set('collection', params.collection);
    if (params?.search) searchParams.set('search', params.search);
    if (params?.sort) searchParams.set('sort', params.sort);
    searchParams.set('per_page', String(params?.perPage || 100));

    const res = await fetch(`${API_BASE}/products?${searchParams.toString()}`, {
      next: { revalidate: 0 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch products: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data)) {
      return json.data.map((item: any) => {
        const defaultImg = item.featured_image_url || 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
        const colors = (Array.isArray(item.colors) && item.colors.length > 0)
          ? item.colors
          : [{ name: 'Signature', hex: '#0B132B', image: defaultImg }];
        const sizes = (Array.isArray(item.sizes) && item.sizes.length > 0)
          ? item.sizes
          : ['All Size'];

        return {
          id: String(item.id),
          slug: item.slug,
          title: item.name,
          subtitle: item.subtitle || '',
          badge: item.badge,
          isNewDrop: item.badge?.includes('NEW') || item.badge?.includes('DROP') || Number(item.id) >= 10,
          isBestSeller: item.badge?.includes('BEST') || item.badge?.includes('TOP') || (item.sold_count && item.sold_count > 1000),
          rating: item.rating,
          reviewCount: item.review_count,
          soldCount: item.sold_count,
          originalPrice: item.price?.compare_at || item.price?.max || item.price?.min,
          price: item.price?.min || 0,
          priceMin: item.price?.min,
          priceMax: item.price?.max,
          discountPercentage: item.price?.discount_percentage || 0,
          category: item.category?.name || 'Streetwear',
          material: item.material || item.specifications?.Material || '',
          gsm: item.gsm || (item.specifications?.Gramasi ? parseInt(item.specifications.Gramasi) : 300),
          fit: item.fit || item.specifications?.Cutting || item.specifications?.['Fit / Cutting'] || '',
          origin: 'Bandung, Indonesia',
          stockTotal: item.variants_count * 10,
          colors,
          sizes,
          gallery: [defaultImg],
          features: [],
          specifications: item.specifications || {},
          variants: item.variants || [],
        };
      });
    }
  } catch (err) {
    console.warn('Backend API unreachable, falling back to local rich catalog:', err);
  }

  return productsCatalog;
}

export async function fetchProductDetailFromApi(identifier: string): Promise<Product | null> {
  try {
    const res = await fetch(`${API_BASE}/products/${identifier}`, {
      next: { revalidate: 0 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Product not found: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && json.data) {
      const item = json.data;
      return {
        id: String(item.id),
        slug: item.slug,
        title: item.name,
        subtitle: item.subtitle || '',
        isNewDrop: item.badge?.includes('NEW') || item.badge?.includes('DROP'),
        isBestSeller: item.badge?.includes('BEST') || item.badge?.includes('TOP'),
        rating: item.rating,
        reviewCount: item.review_count,
        soldCount: item.sold_count,
        originalPrice: item.price?.compare_at || item.price?.max || item.price?.min,
        price: item.price?.min || 0,
        priceMin: item.price?.min,
        priceMax: item.price?.max,
        discountPercentage: item.price?.compare_at && item.price.min < item.price.compare_at
          ? Math.round(((item.price.compare_at - item.price.min) / item.price.compare_at) * 100)
          : 0,
        description: item.description || '',
        category: item.category?.name || 'Streetwear',
        material: item.material || item.specifications?.Material || '',
        gsm: item.gsm || (item.specifications?.Gramasi ? parseInt(item.specifications.Gramasi) : 300),
        fit: item.fit || item.specifications?.Cutting || item.specifications?.['Fit / Cutting'] || '',
        origin: item.origin || 'Bandung, Indonesia',
        stockTotal: item.total_stock || item.available_stock || 100,
        colors: item.colors || [],
        sizes: item.sizes || [],
        gallery: item.gallery_images?.map((g: any) => g.image_url) || [item.featured_image_url],
        features: item.features || [],
        specifications: item.specifications || {},
        variants: item.variants || []
      };
    }
  } catch (err) {
    console.warn(`Backend API detail failed for ${identifier}, using local catalog fallback:`, err);
  }

  return productsCatalog.find(p => p.id === identifier || p.slug === identifier) || null;
}

export async function fetchShippingRatesFromApi(payload: {
  destination_postal_code?: string;
  destination_city?: string;
  items?: any[];
}): Promise<ShippingOption[]> {
  try {
    const res = await fetch(`${API_BASE}/shipping/rates`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(payload),
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch shipping rates: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data) && json.data.length > 0) {
      return json.data.map((item: any) => ({
        id: item.id || `biteship-${item.courier_code}-${item.service_code}`,
        name: item.name || `${item.courier} (${item.service})`,
        service: item.service || item.service_name || 'Standard',
        courier: item.courier || item.courier_name || 'Kurir',
        courier_code: item.courier_code,
        service_code: item.service_code,
        cost: Number(item.cost) || 15000,
        formatted_cost: item.formatted_cost,
        etd: item.etd || '1-2 Hari Kerja',
        tier: item.tier || 'standard',
        available: item.available !== false,
        disabled: item.disabled === true,
        disabledReason: item.disabled_reason || undefined,
      }));
    }
  } catch (err) {
    console.warn('Biteship rates API failed or unreachable, using local fallback:', err);
  }

  return shippingCouriers;
}

export async function fetchPaymentMethodsFromApi(amount: number = 100000): Promise<PaymentMethod[]> {
  try {
    const res = await fetch(`${API_BASE}/payments/methods?amount=${amount}`, {
      next: { revalidate: 60 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch payment methods: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data) && json.data.length > 0) {
      // Curated channels to display prominently
      const priorityOrder = ['SP', 'BC', 'M2', 'I1', 'BR', 'BT', 'B1', 'VC', 'DA', 'OV', 'COD'];

      const mapped = json.data.map((item: any) => {
        const code = (item.code || '').toUpperCase();
        let category: 'qris' | 'va' | 'card' | 'paylater' | 'cod' = 'va';
        let name = item.name || code;
        let desc = 'Verifikasi pembayaran otomatis 24 jam via Duitku';
        let bankName = item.name || 'Bank';

        switch (code) {
          case 'SP':
          case 'QR':
          case 'NQ':
          case 'LQ':
          case 'GQ':
            category = 'qris';
            name = 'QRIS Instant Pay (Semua Bank & E-Wallet)';
            desc = 'Scan otomatis via BCA, Mandiri, Gopay, OVO, Dana, ShopeePay (Bebas Biaya Admin)';
            break;
          case 'BC':
            category = 'va';
            name = 'BCA Virtual Account';
            desc = 'Verifikasi instan otomatis 24 jam via BCA Mobile / myBCA / KlikBCA / ATM';
            bankName = 'BCA';
            break;
          case 'M2':
            category = 'va';
            name = 'Mandiri Virtual Account';
            desc = 'Bayar instan via aplikasi Livin by Mandiri atau ATM Mandiri';
            bankName = 'Mandiri';
            break;
          case 'I1':
            category = 'va';
            name = 'BNI Virtual Account';
            desc = 'Bayar instan via BNI Mobile Banking / ATM BNI';
            bankName = 'BNI';
            break;
          case 'BR':
            category = 'va';
            name = 'BRI Virtual Account (BRIVA)';
            desc = 'Bayar instan via aplikasi BRImo / ATM BRI seluruh Indonesia';
            bankName = 'BRI';
            break;
          case 'BT':
            category = 'va';
            name = 'Permata Virtual Account';
            desc = 'Bayar via PermataMobile X / ATM Permata';
            bankName = 'Permata';
            break;
          case 'B1':
            category = 'va';
            name = 'CIMB Niaga Virtual Account';
            desc = 'Bayar via OCTO Mobile / OCTO Clicks / ATM CIMB';
            bankName = 'CIMB Niaga';
            break;
          case 'VC':
            category = 'card';
            name = 'Kartu Kredit / Debit (Visa & Mastercard)';
            desc = 'Proteksi enkripsi 256-bit SSL & 3D Secure OTP';
            break;
          case 'DA':
            category = 'qris';
            name = 'DANA E-Wallet';
            desc = 'Pembayaran instan via aplikasi DANA';
            break;
          case 'OV':
            category = 'qris';
            name = 'OVO Instant Pay';
            desc = 'Pembayaran instan via aplikasi OVO';
            break;
          case 'COD':
            category = 'cod';
            name = 'COD (Bayar Tunai di Tempat)';
            desc = 'Bayar tunai ke kurir saat paket sampai di alamat Anda';
            break;
          default:
            category = 'va';
            desc = `Verifikasi instan otomatis 24 jam via ${item.name}`;
            break;
        }

        return {
          id: `duitku-${code.toLowerCase()}`,
          name,
          duitkuCode: code,
          category,
          description: desc,
          fee: item.fee || 0,
          image: item.image,
          bankName,
          accountNumber: category === 'va' ? '8271081234567890' : undefined
        };
      });

      // Ensure COD is present
      if (!mapped.some((m: PaymentMethod) => m.duitkuCode === 'COD')) {
        mapped.push({
          id: 'cod',
          duitkuCode: 'COD',
          name: 'COD (Bayar Tunai di Tempat)',
          description: 'Bayar tunai ke kurir saat paket sampai di alamat Anda',
          category: 'cod'
        });
      }

      // Filter and sort by priority order
      const prioritySet = new Set(priorityOrder);
      const filtered = mapped.filter((m: PaymentMethod) => prioritySet.has(m.duitkuCode || ''));

      filtered.sort((a: PaymentMethod, b: PaymentMethod) => {
        const idxA = priorityOrder.indexOf(a.duitkuCode || '');
        const idxB = priorityOrder.indexOf(b.duitkuCode || '');
        return (idxA === -1 ? 99 : idxA) - (idxB === -1 ? 99 : idxB);
      });

      return filtered.length > 0 ? filtered : mapped;
    }
  } catch (err) {
    console.warn('Duitku payment methods API unreachable, using local fallback:', err);
  }

  return paymentGateways;
}

export async function fetchPublicVouchersFromApi(): Promise<Voucher[]> {
  try {
    const res = await fetch(`${API_BASE}/vouchers/public`, {
      next: { revalidate: 60 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch vouchers: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data)) {
      return json.data.map((item: any) => ({
        id: item.id,
        code: item.code,
        title: item.title || item.name,
        name: item.name,
        description: item.description || '',
        minSpend: Number(item.min_spend || item.minSpend || 0),
        min_spend: Number(item.min_spend || item.minSpend || 0),
        discount: Number(item.discount || item.amount || 0),
        amount: Number(item.amount || 0),
        maxDiscount: item.max_discount ? Number(item.max_discount) : undefined,
        max_discount: item.max_discount ? Number(item.max_discount) : undefined,
        formatted_discount: item.formatted_discount,
        type: (item.type === 'free_shipping' ? 'shipping' : item.type === 'fixed_amount' ? 'fixed' : item.type) as 'fixed' | 'percentage' | 'shipping',
        applied: false,
        validUntil: item.valid_until,
      }));
    }
  } catch (err) {
    console.warn('Backend API vouchers unreachable, fallback to local data:', err);
  }

  return availableVouchers;
}

export async function validateVoucherApi(params: {
  code: string;
  subtotal: number;
  shipping_cost?: number;
  email?: string;
  phone?: string;
}): Promise<{
  success: boolean;
  message: string;
  discount_amount: number;
  voucher?: any;
}> {
  try {
    const res = await fetch(`${API_BASE}/vouchers/validate`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
      },
      body: JSON.stringify(params),
    });

    const json = await res.json();

    return {
      success: Boolean(json.success || res.ok),
      message: json.message || (res.ok ? 'Voucher berhasil diterapkan' : 'Voucher tidak valid atau tidak memenuhi syarat.'),
      discount_amount: Number(json.data?.discount_amount || 0),
      voucher: json.data?.voucher,
    };
  } catch (err: any) {
    return {
      success: false,
      message: err.message || 'Gagal menghubungi server validasi promo.',
      discount_amount: 0,
    };
  }
}

