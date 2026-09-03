import { Product, ProductVariant, CatalogCollection } from '../types';
import { productsCatalog } from '../data/products';
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
