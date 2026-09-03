import { Product, ProductVariant } from '../types';
import { productsCatalog } from '../data/products';

const API_BASE = process.env.NEXT_PUBLIC_BACKEND_API_URL || process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api/v1';

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
    if (params?.perPage) searchParams.set('per_page', String(params.perPage));

    const res = await fetch(`${API_BASE}/products?${searchParams.toString()}`, {
      next: { revalidate: 0 },
      cache: 'no-store',
    });

    if (!res.ok) {
      throw new Error(`Failed to fetch products: ${res.statusText}`);
    }

    const json = await res.json();
    if (json.success && Array.isArray(json.data)) {
      return json.data.map((item: any) => ({
        id: String(item.id),
        slug: item.slug,
        title: item.name,
        subtitle: item.subtitle || '',
        badge: item.badge,
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
        colors: item.colors || [],
        sizes: item.sizes || [],
        gallery: [item.featured_image_url],
        features: [],
        specifications: item.specifications || {},
      }));
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
