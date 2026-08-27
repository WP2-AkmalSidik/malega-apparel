export interface BrandValue {
  number: string;
  title: string;
  headline: string;
  description: string;
}

export const BRAND_VALUES: BrandValue[] = [
  {
    number: "01",
    title: "Thoughtful Proportion",
    headline: "Engineered for Balance & Flow",
    description: "Every cut begins with deliberate geometry. We fine-tune shoulder drops, chest ease, and hemlines so our garments rest naturally on diverse bodies without feeling cumbersome.",
  },
  {
    number: "02",
    title: "Everyday Versatility",
    headline: "Seamless Transition Across Moments",
    description: "Our pieces are made to move effortlessly from calm morning routines to focused workdays and evening gatherings. Minimalism that adapts to your rhythm.",
  },
  {
    number: "03",
    title: "Material Integrity",
    headline: "Substance You Can Feel",
    description: "We prioritize dense combed cotton yarns, refined linen weaves, and robust rib collars that maintain shape, rich color depth, and tactile luxury wash after wash.",
  },
];

export interface SocialGalleryItem {
  id: string;
  caption: string;
  tag: string;
  image: string;
  alt: string;
}

export const SOCIAL_GALLERY: SocialGalleryItem[] = [
  {
    id: "gallery-01",
    caption: "Midnight Navy Silhouette Study",
    tag: "CAMPAIGN",
    image: "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=800&auto=format&fit=crop&q=85",
    alt: "Malega silhouette and tailoring detail",
  },
  {
    id: "gallery-02",
    caption: "Modern Modest Form in Soft Ivory",
    tag: "MODEST",
    image: "https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&auto=format&fit=crop&q=85",
    alt: "Contemporary modest garment showcase",
  },
  {
    id: "gallery-03",
    caption: "Heavyweight Cotton Knit & Tactile Texture",
    tag: "DETAIL",
    image: "https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&auto=format&fit=crop&q=85",
    alt: "Fabric and stitch detail",
  },
  {
    id: "gallery-04",
    caption: "Layering in Natural Light",
    tag: "LIFESTYLE",
    image: "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=800&auto=format&fit=crop&q=85",
    alt: "Outerwear layering composition",
  },
  {
    id: "gallery-05",
    caption: "Architectural Lines & Neutral Palettes",
    tag: "STUDIO",
    image: "https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&auto=format&fit=crop&q=85",
    alt: "Studio shoot with architectural lighting",
  },
  {
    id: "gallery-06",
    caption: "Atelier Essentials & Finishing Touches",
    tag: "OBJECTS",
    image: "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=800&auto=format&fit=crop&q=85",
    alt: "Lifestyle accessories and caps",
  },
];
