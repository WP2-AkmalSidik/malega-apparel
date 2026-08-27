export interface CollectionItem {
  id: string;
  number: string;
  title: string;
  tagline: string;
  description: string;
  tag: string;
  image: string;
  alt: string;
  href: string;
}

export const FEATURED_COLLECTIONS: CollectionItem[] = [
  {
    id: "essentials",
    number: "01",
    title: "Everyday Essentials",
    tagline: "Form & Function in Daily Movement",
    description: "Refined basics designed with deliberate proportions, premium heavyweight combed cotton, and timeless silhouettes built for lasting everyday wear.",
    tag: "CORE LINE",
    image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=85",
    alt: "Malega Everyday Essentials collection - clean minimal silhouettes",
    href: "#categories",
  },
  {
    id: "modest",
    number: "02",
    title: "Malega Modest",
    tagline: "Contemporary Modest Silhouette",
    description: "Modern koko shirts, structured tunics, and modest essentials crafted with understated elegance, breathable luxury fabrics, and sophisticated drape.",
    tag: "SEASONAL CAPSULE",
    image: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=900&auto=format&fit=crop&q=85",
    alt: "Malega Modest collection - contemporary koko and modest wear",
    href: "#modest",
  },
  {
    id: "outerwear",
    number: "03",
    title: "Outerwear & Layers",
    tagline: "Structured Textures & Protection",
    description: "Versatile overshirts, French terry layerings, and lightweight tailored jackets engineered for effortless transitions across occasions and climates.",
    tag: "LAYER SERIES",
    image: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=85",
    alt: "Malega Outerwear & Layering collection",
    href: "#categories",
  },
  {
    id: "signature",
    number: "04",
    title: "Signature Pieces",
    tagline: "Distinctive Silhouettes & Subtle Details",
    description: "Limited releases characterized by architectural cuts, subtle champagne accents, and elevated craftsmanship that define the Malega design language.",
    tag: "ATELIER DROP",
    image: "https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=85",
    alt: "Malega Signature Pieces collection",
    href: "#new-arrivals",
  },
];
