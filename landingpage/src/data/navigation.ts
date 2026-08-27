export interface NavItem {
  label: string;
  href: string;
}

export interface FooterSection {
  title: string;
  links: NavItem[];
}

export const MAIN_NAV: NavItem[] = [
  { label: "Collection", href: "#collections" },
  { label: "Categories", href: "#categories" },
  { label: "New Arrivals", href: "#new-arrivals" },
  { label: "The Edit", href: "#editorial" },
  { label: "Modest", href: "#modest" },
  { label: "About", href: "#about" },
];

export const FOOTER_DIRECTORY: FooterSection[] = [
  {
    title: "Collection",
    links: [
      { label: "Everyday Essentials", href: "#collections" },
      { label: "Malega Modest", href: "#modest" },
      { label: "Outerwear & Layers", href: "#collections" },
      { label: "Signature Pieces", href: "#collections" },
      { label: "New Releases", href: "#new-arrivals" },
    ],
  },
  {
    title: "Categories",
    links: [
      { label: "T-Shirts & Tops", href: "#categories" },
      { label: "Koko & Modest Wear", href: "#modest" },
      { label: "Tailored Shirts", href: "#categories" },
      { label: "Jackets & Outerwear", href: "#categories" },
      { label: "Trousers & Pants", href: "#categories" },
    ],
  },
  {
    title: "Brand & Atelier",
    links: [
      { label: "Our Philosophy", href: "#statement" },
      { label: "Craft & Material Integrity", href: "#values" },
      { label: "The Story", href: "#about" },
      { label: "Campaign & Lookbook", href: "#editorial" },
      { label: "Sustainability & Care", href: "#values" },
    ],
  },
  {
    title: "Client Care",
    links: [
      { label: "Direct Assistance", href: "https://wa.me/6281234567890?text=Halo%20Malega%20Apparel" },
      { label: "Size & Fit Consultation", href: "https://wa.me/6281234567890?text=Halo%20Malega%20Apparel%20Size%20Guide" },
      { label: "Shipping & Delivery", href: "#" },
      { label: "Return Policy", href: "#" },
    ],
  },
];

export const SOCIAL_LINKS = [
  { label: "Instagram", href: "https://instagram.com/malega.apparel", handle: "@malegaapparel" },
  { label: "TikTok", href: "https://tiktok.com/@malega.apparel", handle: "@malega.apparel" },
  { label: "WhatsApp Concierge", href: "https://wa.me/6281234567890", handle: "+62 812-3456-7890" },
];
