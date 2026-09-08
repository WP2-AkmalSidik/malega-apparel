import { products } from '../../../data/products';

/**
 * Helper to extract clean product title, variant color, size, and resolve
 * high-resolution product artwork from the local catalog.
 */
export const parseItemDetails = (productName: string, variantTitle: string, sku: string) => {
  let title = productName;
  let color = '';
  let size = '';

  const cleanVariant = variantTitle || '';
  const cleanName = productName || '';

  // If productName is generic Malega Apparel, parse from variantTitle
  if (cleanName.toLowerCase().includes('malega') || cleanName.length <= 15) {
    if (cleanVariant.includes(' - ')) {
      const [parsedTitle, rest] = cleanVariant.split(' - ');
      if (parsedTitle && parsedTitle.trim().length > 3) {
        title = parsedTitle.trim();
      }
      if (rest) {
        const parts = rest.split('/').map((s) => s.trim());
        color = parts.length > 2 ? `${parts[0]} / ${parts[1]}` : parts[0] || '';
        size = parts.length > 2 ? parts.slice(2).join(' / ') : parts[1] || '';
      }
    } else if (cleanVariant.includes('/')) {
      const parts = cleanVariant.split('/').map((s) => s.trim());
      color = parts[0] || '';
      size = parts.slice(1).join(' / ') || '';
    }
  } else {
    // Specific product title, parse variantTitle for color / size
    if (cleanVariant.includes(' - ')) {
      const [, rest] = cleanVariant.split(' - ');
      if (rest) {
        const parts = rest.split('/').map((s) => s.trim());
        color = parts.length > 2 ? `${parts[0]} / ${parts[1]}` : parts[0] || '';
        size = parts.length > 2 ? parts.slice(2).join(' / ') : parts[1] || '';
      }
    } else if (cleanVariant.includes('/')) {
      const parts = cleanVariant.split('/').map((s) => s.trim());
      color = parts[0] || '';
      size = parts.slice(1).join(' / ') || '';
    } else {
      color = cleanVariant;
    }
  }

  // Resolve real high-resolution product artwork from catalog
  const normTitle = title.toLowerCase();
  const normSku = sku.toLowerCase();
  let image = '';

  const matchedProduct = products.find((p) =>
    normTitle.includes(p.title.toLowerCase()) ||
    p.title.toLowerCase().includes(normTitle) ||
    (p.slug && normSku.includes(p.slug.toLowerCase())) ||
    normSku.includes(p.id.toLowerCase())
  );

  if (matchedProduct) {
    const matchedColor = matchedProduct.colors?.find(
      (c) =>
        color &&
        (c.name.toLowerCase().includes(color.toLowerCase()) ||
          color.toLowerCase().includes(c.name.toLowerCase()))
    );
    image = matchedColor?.image || matchedProduct.colors?.[0]?.image || matchedProduct.gallery?.[0] || '';
  }

  // Fallback high-fashion assets if catalog match is approximate
  if (!image) {
    if (normTitle.includes('cap') || normTitle.includes('topi')) {
      image = 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80';
    } else if (normTitle.includes('oxford') || normTitle.includes('shirt') || normTitle.includes('kemeja')) {
      image = 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80';
    } else if (normTitle.includes('chino') || normTitle.includes('pant') || normTitle.includes('celana')) {
      image = 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80';
    } else if (normTitle.includes('belt') || normTitle.includes('ikat pinggang')) {
      image = 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80';
    } else if (normTitle.includes('robe') || normTitle.includes('kimono') || normTitle.includes('jacket') || normTitle.includes('coat')) {
      image = 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80';
    } else {
      image = 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80';
    }
  }

  return { title, color, size, image };
};
