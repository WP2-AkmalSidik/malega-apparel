export interface CategoryItem {
  id: string;
  name: string;
  subtitle: string;
  itemCount: string;
  image: string;
  alt: string;
  href: string;
}

export const CATEGORIES: CategoryItem[] = [
  {
    id: "t-shirts",
    name: "T-Shirts & Tops",
    subtitle: "Heavyweight 240–300GSM Cotton",
    itemCount: "12 Pieces",
    image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=85",
    alt: "Malega T-Shirts and tops collection",
    href: "#new-arrivals",
  },
  {
    id: "koko-modest",
    name: "Koko & Modest Wear",
    subtitle: "Contemporary Mandarin & Band Collars",
    itemCount: "8 Pieces",
    image: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Modern Koko and Modest Apparel",
    href: "#modest",
  },
  {
    id: "shirts",
    name: "Tailored Shirts",
    subtitle: "Crisp Oxford & Relaxed Silhouettes",
    itemCount: "10 Pieces",
    image: "https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Tailored and Relaxed Shirts",
    href: "#new-arrivals",
  },
  {
    id: "outerwear",
    name: "Jackets & Outerwear",
    subtitle: "French Terry, Overshirts & Knitwear",
    itemCount: "7 Pieces",
    image: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Outerwear & Layering Pieces",
    href: "#new-arrivals",
  },
  {
    id: "trousers",
    name: "Trousers & Pants",
    subtitle: "Pleated Trousers & Relaxed Fit Chinos",
    itemCount: "6 Pieces",
    image: "https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Trousers and casual bottoms",
    href: "#new-arrivals",
  },
  {
    id: "essentials",
    name: "Daily Essentials",
    subtitle: "Atelier Accessories & Baseline Pieces",
    itemCount: "9 Pieces",
    image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=85",
    alt: "Malega Daily Essentials and Accessories",
    href: "#new-arrivals",
  },
];
