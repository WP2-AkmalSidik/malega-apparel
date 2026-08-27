export interface ProductItem {
  id: string;
  name: string;
  category: string;
  color: string;
  price: number;
  formattedPrice: string;
  tag?: string;
  image: string;
  hoverImage: string;
  alt: string;
  href: string;
}

export const NEW_ARRIVALS: ProductItem[] = [
  {
    id: "mlg-tee-01",
    name: "Signature Boxy Tee",
    category: "Everyday Essentials",
    color: "Midnight Navy",
    price: 219000,
    formattedPrice: "Rp 219.000",
    tag: "SIGNATURE",
    image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Signature Boxy Tee in Midnight Navy",
    href: "#",
  },
  {
    id: "mlg-koko-01",
    name: "Modern Mandarin Koko",
    category: "Malega Modest",
    color: "Soft Ivory",
    price: 289000,
    formattedPrice: "Rp 289.000",
    tag: "NEW CAPSULE",
    image: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Modern Mandarin Koko in Soft Ivory",
    href: "#modest",
  },
  {
    id: "mlg-shirt-01",
    name: "Structured Relaxed Overshirt",
    category: "Outerwear & Layers",
    color: "Charcoal Slate",
    price: 349000,
    formattedPrice: "Rp 349.000",
    tag: "TAILORED",
    image: "https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Structured Relaxed Overshirt",
    href: "#",
  },
  {
    id: "mlg-hoodie-01",
    name: "French Terry Layer Hoodie",
    category: "Outerwear & Layers",
    color: "Warm Sand",
    price: 389000,
    formattedPrice: "Rp 389.000",
    image: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=85",
    alt: "Malega French Terry Layer Hoodie",
    href: "#",
  },
  {
    id: "mlg-pants-01",
    name: "Pleated Relaxed Trouser",
    category: "Bottoms",
    color: "Deep Obsidian",
    price: 329000,
    formattedPrice: "Rp 329.000",
    image: "https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Pleated Relaxed Trouser",
    href: "#",
  },
  {
    id: "mlg-koko-02",
    name: "Contemporary Minimal Kurta",
    category: "Malega Modest",
    color: "Midnight Navy",
    price: 309000,
    formattedPrice: "Rp 309.000",
    tag: "MODEST ESSENTIAL",
    image: "https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Contemporary Minimal Kurta",
    href: "#modest",
  },
  {
    id: "mlg-tee-02",
    name: "Heavyweight Core Tee",
    category: "Everyday Essentials",
    color: "Soft Ivory",
    price: 199000,
    formattedPrice: "Rp 199.000",
    image: "https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Heavyweight Core Tee in Soft Ivory",
    href: "#",
  },
  {
    id: "mlg-acc-01",
    name: "Atelier Monogram Cap",
    category: "Essentials",
    color: "Champagne Gold Detail",
    price: 139000,
    formattedPrice: "Rp 139.000",
    image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=85",
    hoverImage: "https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Atelier Monogram Cap",
    href: "#",
  },
];
