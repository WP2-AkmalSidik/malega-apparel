import { Product, ProductVariant, Voucher, ShippingOption, PaymentMethod, Address } from '../types';

function generateVariantsForProduct(
  p: Omit<Product, 'variants'>,
  colorPriceExtras: { [colorName: string]: number } = {},
  sizePriceExtras: { [size: string]: number } = {}
): ProductVariant[] {
  const skuPrefix = 'MLG-' + p.slug.replace(/[^a-z0-9]/gi, '').substring(0, 4).toUpperCase();
  const variants: ProductVariant[] = [];

  for (const c of p.colors) {
    const cExtra = colorPriceExtras[c.name] ?? c.priceExtra ?? 0;
    for (const s of p.sizes) {
      const sExtra = sizePriceExtras[s] ?? 0;
      const finalPrice = p.price + cExtra + sExtra;
      const compareAt = p.originalPrice ? (p.originalPrice + cExtra + sExtra) : undefined;
      const sku = `${skuPrefix}-${c.name.substring(0, 3).toUpperCase()}-${s.replace(/\s+/g, '')}`;

      variants.push({
        id: `${p.id}-${sku}`,
        sku,
        title: `${p.title} - ${c.name} / ${s}`,
        color: { name: c.name, hex: c.hex, image: c.image },
        size: s,
        price: finalPrice,
        formattedPrice: `Rp ${finalPrice.toLocaleString('id-ID')}`,
        compareAtPrice: compareAt,
        weightGrams: p.gsm ? p.gsm + 50 : 350,
        availableStock: 8,
        isInStock: true
      });
    }
  }

  return variants;
}

const rawProducts: Product[] = [
  {
    id: 'mlg-001',
    slug: 'obsidian-heavyweight-boxy-tee-300gsm',
    title: 'Obsidian Heavyweight Boxy Tee 300GSM',
    subtitle: 'Signature Drop Shoulder • Combed 300GSM Cotton • High Density Stitching',
    isNewDrop: true,
    isBestSeller: true,
    rating: 4.9,
    reviewCount: 1420,
    soldCount: 3840,
    originalPrice: 289000,
    price: 229000,
    priceMin: 229000,
    priceMax: 264000,
    discountPercentage: 20,
    category: 'T-Shirts',
    material: '100% Combed Heavy Cotton 300 GSM',
    gsm: 300,
    fit: 'Modern Boxy Drop Shoulder Oversized',
    origin: 'Bandung, Indonesia',
    stockTotal: 86,
    colors: [
      {
        name: 'Onyx Black',
        hex: '#111827',
        image: 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Washed Olive',
        hex: '#3f4834',
        image: 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80',
        priceExtra: 10000
      },
      {
        name: 'Slate Charcoal',
        hex: '#334155',
        image: 'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Vintage Acid Wash',
        hex: '#64748b',
        image: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
        priceExtra: 20000
      }
    ],
    sizes: ['S', 'M', 'L', 'XL', 'XXL'],
    sizePriceExtra: {
      'S': 0,
      'M': 0,
      'L': 0,
      'XL': 0,
      'XXL': 15000
    },
    gallery: [
      'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Material 300GSM Heavy Cotton bebas susut & tebal',
      'Kerah Rib 3.5cm ganda anti-melar',
      'Pola cutting drop-shoulder boxy proporsional',
      'Pewarnaan Reactive Dye ramah lingkungan dan awet'
    ],
    description: `Obsidian Heavyweight Boxy Tee adalah karya esensial streetwear dari Malega Apparel. Dikonstruksi khusus menggunakan 100% Combed Heavy Cotton 300GSM murni yang menghadirkan tekstur kokoh, jatuh kain yang rapi, dan kenyamanan termal untuk gaya hidup aktif perkotaan.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Gramasi': '300 GSM Heavyweight',
      'Material': '100% Pure Combed Cotton',
      'Cutting': 'Boxy Fit / Drop Shoulder',
      'Kerah': '3.5cm Reinforced Rib Collar',
      'Perawatan': 'Cuci dengan air dingin, setrika temperatur sedang'
    }
  },
  {
    id: 'mlg-002',
    slug: 'minimalist-fleece-boxy-hoodie-380gsm',
    title: 'Minimalist Boxy Fleece Hoodie 380GSM',
    subtitle: 'Heavy French Terry • Double Layer Hood • Hidden Pouch',
    isNewDrop: true,
    isBestSeller: true,
    rating: 5.0,
    reviewCount: 890,
    soldCount: 1950,
    originalPrice: 549000,
    price: 449000,
    priceMin: 449000,
    priceMax: 489000,
    discountPercentage: 18,
    category: 'Outerwear',
    material: '100% French Terry Cotton 380 GSM',
    gsm: 380,
    fit: 'Boxy Relaxed Silhouette',
    origin: 'Bandung, Indonesia',
    stockTotal: 42,
    colors: [
      {
        name: 'Midnight Black',
        hex: '#0f172a',
        image: 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Washed Taupe',
        hex: '#78716c',
        image: 'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=900&auto=format&fit=crop&q=80',
        priceExtra: 15000
      },
      {
        name: 'Forest Olive',
        hex: '#2e3828',
        image: 'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80',
        priceExtra: 10000
      }
    ],
    sizes: ['S', 'M', 'L', 'XL', 'XXL'],
    sizePriceExtra: {
      'S': 0,
      'M': 0,
      'L': 0,
      'XL': 0,
      'XXL': 25000
    },
    gallery: [
      'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1512436991641-6745cdb1723f?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Material 380GSM French Terry ultra-lembut',
      'Double-layer hood tanpa tali untuk tampilan ultra-clean',
      'Kantung kangguru seamless internal',
      'Manset rib elastis tebal'
    ],
    description: `Dibuat untuk ketahanan dan siluet modern yang bersih, Minimalist Boxy Fleece Hoodie memadukan kenyamanan kain French Terry premium dengan potongan tegas pada bahu dan torso.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Gramasi': '380 GSM Heavy French Terry',
      'Material': '100% Cotton French Terry',
      'Hood': 'Double Layer Seamless',
      'Fit': 'Structured Boxy Cut'
    }
  },
  {
    id: 'mlg-003',
    slug: 'tactical-ripstop-utility-cargo-pants',
    title: 'Tactical Ripstop Utility Cargo Pants',
    subtitle: 'Reinforced Diamond Ripstop • 6 Modular Pockets • Adjustable Cuff',
    isNewDrop: false,
    isBestSeller: true,
    rating: 4.8,
    reviewCount: 650,
    soldCount: 1420,
    originalPrice: 489000,
    price: 399000,
    priceMin: 399000,
    priceMax: 429000,
    discountPercentage: 18,
    category: 'Bottoms',
    material: 'Cotton Twill Ripstop 280 GSM',
    gsm: 280,
    fit: 'Tapered Utility Fit',
    origin: 'Bandung, Indonesia',
    stockTotal: 55,
    colors: [
      {
        name: 'Slate Charcoal',
        hex: '#1e293b',
        image: 'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Desert Khaki',
        hex: '#a89f91',
        image: 'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=80',
        priceExtra: 10000
      }
    ],
    sizes: ['28', '30', '32', '34', '36'],
    sizePriceExtra: {
      '28': 0,
      '30': 0,
      '32': 0,
      '34': 15000,
      '36': 20000
    },
    gallery: [
      'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Bahan Ripstop tahan robek & breathable',
      '6 saku utilitas ergonomis dengan penutup magnetik',
      'Karet pinggang elastis dengan buckle slider',
      'Tali serut pergelangan kaki (adjustable cuff)'
    ],
    description: `Celana kargo utilitas yang menggabungkan fungsionalitas outdoor dengan siluet streetwear modern. Dilengkapi konstruksi jahitan bar-tack pada area bertekanan tinggi.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Material': 'Cotton Twill Diamond Ripstop',
      'Pockets': '6 Utility Compartments',
      'Hardware': 'YKK Zippers & D-Ring'
    }
  },
  {
    id: 'mlg-004',
    slug: 'selvedge-denim-workwear-overshirt-14oz',
    title: 'Selvedge Denim Workwear Overshirt 14oz',
    subtitle: 'Raw Indigo Selvedge • Antique Brass Hardware • Boxy Fit',
    isNewDrop: true,
    isBestSeller: false,
    rating: 4.9,
    reviewCount: 310,
    soldCount: 780,
    originalPrice: 629000,
    price: 529000,
    priceMin: 529000,
    priceMax: 574000,
    discountPercentage: 15,
    category: 'Outerwear',
    material: '14oz Raw Rigid Selvedge Denim',
    gsm: 420,
    fit: 'Structured Boxy Overshirt',
    origin: 'Bandung, Indonesia',
    stockTotal: 28,
    colors: [
      {
        name: 'Raw Deep Indigo',
        hex: '#1e3a8a',
        image: 'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Acid Washed Grey',
        hex: '#475569',
        image: 'https://images.unsplash.com/photo-1543087903-1ac2ec7aa8c5?w=900&auto=format&fit=crop&q=80',
        priceExtra: 30000
      }
    ],
    sizes: ['S', 'M', 'L', 'XL'],
    sizePriceExtra: {
      'S': 0,
      'M': 0,
      'L': 0,
      'XL': 15000
    },
    gallery: [
      'https://images.unsplash.com/photo-1576995853123-5a10305d93c0?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1543087903-1ac2ec7aa8c5?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Bahan 14oz Pure Selvedge Denim',
      'Kancing logam antik berukir logo Malega',
      'Dual chest flap pockets',
      'Aksen selvedge line merah pada bagian placket dalam'
    ],
    description: `Workwear overshirt dengan selvedge denim murni yang akan menghasilkan fade unik dan personal seiring pemakaian Anda.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Berat Kain': '14 oz Selvedge',
      'Fitting': 'Relaxed Overshirt',
      'Hardware': 'Custom Antique Brass'
    }
  },
  {
    id: 'mlg-005',
    slug: 'vintage-washed-drop-shoulder-tee',
    title: 'Vintage Washed Drop-Shoulder Tee 280GSM',
    subtitle: 'Sun-Faded Wash • Distressed Edge • 280GSM Combed',
    isNewDrop: false,
    isBestSeller: true,
    rating: 4.8,
    reviewCount: 920,
    soldCount: 2450,
    originalPrice: 269000,
    price: 219000,
    priceMin: 219000,
    priceMax: 249000,
    discountPercentage: 18,
    category: 'T-Shirts',
    material: '100% Combed Cotton Vintage Wash',
    gsm: 280,
    fit: 'Drop Shoulder Boxy',
    origin: 'Bandung, Indonesia',
    stockTotal: 64,
    colors: [
      {
        name: 'Washed Charcoal',
        hex: '#334155',
        image: 'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Washed Moss',
        hex: '#3d4a36',
        image: 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80',
        priceExtra: 10000
      }
    ],
    sizes: ['S', 'M', 'L', 'XL', 'XXL'],
    sizePriceExtra: {
      'S': 0,
      'M': 0,
      'L': 0,
      'XL': 0,
      'XXL': 15000
    },
    gallery: [
      'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Proses stone wash & acid wash eksklusif',
      'Sentuhan lembut bertekstur vintage',
      'Pola fitting proporsional',
      'Kerah rib anti-melar'
    ],
    description: `Memberikan estetika vintage autentik dengan kelembutan maksimal. Setiap lembar memiliki gradasi wash yang unik dan berkarakter.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Gramasi': '280 GSM Vintage',
      'Wash': 'Garment Stone Wash'
    }
  },
  {
    id: 'mlg-006',
    slug: 'structured-minimal-6-panel-cap',
    title: 'Structured Minimal 6-Panel Gold Monogram Cap',
    subtitle: 'Heavy Cotton Twill • 3D Gold Embroidered MA • Brass Buckle',
    isNewDrop: true,
    isBestSeller: false,
    rating: 4.9,
    reviewCount: 410,
    soldCount: 980,
    originalPrice: 229000,
    price: 189000,
    priceMin: 189000,
    priceMax: 199000,
    discountPercentage: 17,
    category: 'Accessories',
    material: 'Premium Heavy Cotton Twill',
    gsm: 320,
    fit: 'Adjustable Unstructured / Semi-Structured',
    origin: 'Bandung, Indonesia',
    stockTotal: 40,
    colors: [
      {
        name: 'Onyx Black / Gold',
        hex: '#0f172a',
        image: 'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      },
      {
        name: 'Navy Sand',
        hex: '#1e293b',
        image: 'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=900&auto=format&fit=crop&q=80',
        priceExtra: 10000
      }
    ],
    sizes: ['All Size (Adjustable)'],
    sizePriceExtra: {
      'All Size (Adjustable)': 0
    },
    gallery: [
      'https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=80',
      'https://images.unsplash.com/photo-1575428652377-a2d80e2277fc?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Bordir 3D High-Density Monogram MA Emas (#CBAC70)',
      'Buckle belakang kuningan solid tahan karat',
      'Visor lengkung presisi dengan 6 jahitan'
    ],
    description: `Aksesori esensial harian dengan detail monogram emas eksklusif yang menyempurnakan outfit streetwear Anda.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Material': 'Heavy Cotton Twill',
      'Hardware': 'Antique Solid Brass'
    }
  },
  {
    id: 'mlg-007',
    slug: 'modular-matte-leather-crossbody-bag',
    title: 'Modular Matte Leather & Cordura Crossbody Bag',
    subtitle: 'Waterproof YKK Hardware • Vegan Leather & Cordura 1000D',
    isNewDrop: true,
    isBestSeller: true,
    rating: 5.0,
    reviewCount: 280,
    soldCount: 650,
    originalPrice: 349000,
    price: 279000,
    priceMin: 279000,
    priceMax: 279000,
    discountPercentage: 20,
    category: 'Accessories',
    material: 'Matte Vegan Leather + Cordura 1000D',
    gsm: 450,
    fit: 'Ergonomic Crossbody',
    origin: 'Bandung, Indonesia',
    stockTotal: 30,
    colors: [
      {
        name: 'Matte Obsidian',
        hex: '#090d16',
        image: 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      }
    ],
    sizes: ['One Size'],
    sizePriceExtra: {
      'One Size': 0
    },
    gallery: [
      'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Kompartemen utama dengan slot tablet 8-inch',
      'Ritsleting YKK Aquaguard tahan air',
      'Strap nylon tebal dengan quick-release magnetic buckle'
    ],
    description: `Tas selempang berdesain taktis dan modern untuk membawa kebutuhan harian esensial dengan aman dan penuh gaya.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Material': 'Cordura 1000D + Vegan Leather',
      'Dimensi': '26 cm x 18 cm x 7 cm'
    }
  },
  {
    id: 'mlg-008',
    slug: 'raw-indigo-relaxed-straight-jeans',
    title: 'Raw Indigo Relaxed Straight Jeans 13.5oz',
    subtitle: 'Sanforized Raw Denim • Chainstitch Hem • Custom Gold Rivets',
    isNewDrop: false,
    isBestSeller: false,
    rating: 4.8,
    reviewCount: 190,
    soldCount: 480,
    originalPrice: 529000,
    price: 439000,
    priceMin: 439000,
    priceMax: 464000,
    discountPercentage: 17,
    category: 'Bottoms',
    material: '13.5oz Sanforized Raw Denim',
    gsm: 400,
    fit: 'Relaxed Straight Leg',
    origin: 'Bandung, Indonesia',
    stockTotal: 35,
    colors: [
      {
        name: 'Deep Raw Indigo',
        hex: '#1e3a8a',
        image: 'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80',
        priceExtra: 0
      }
    ],
    sizes: ['28', '30', '32', '34', '36'],
    sizePriceExtra: {
      '28': 0,
      '30': 0,
      '32': 0,
      '34': 15000,
      '36': 25000
    },
    gallery: [
      'https://images.unsplash.com/photo-1542272604-780c96856592?w=900&auto=format&fit=crop&q=80'
    ],
    features: [
      'Denim murni kaku yang siap fading',
      'Jahitan rantai Union Special pada hem bawah',
      'Rivet tembaga emas berlogo Malega'
    ],
    description: `Celana jeans raw indigo potongan relaxed straight klasik yang memberikan ruang gerak leluasa dan siluet kokoh.`,
    specifications: {
      'Brand': 'Malega Apparel',
      'Berat Kain': '13.5 oz Raw Denim',
      'Cut': 'Relaxed Straight'
    }
  }
];

// Populate variants on each product
export const productsCatalog: Product[] = rawProducts.map(p => {
  const variants = generateVariantsForProduct(p, {}, p.sizePriceExtra);
  const minP = Math.min(...variants.map(v => v.price));
  const maxP = Math.max(...variants.map(v => v.price));

  return {
    ...p,
    priceMin: minP,
    priceMax: maxP,
    variants
  };
});

export const availableVouchers: Voucher[] = [
  {
    code: 'MALEGAVIP15',
    title: 'VIP Gold Member 15% OFF',
    minSpend: 200000,
    discount: 35000,
    type: 'percentage',
    applied: true
  },
  {
    code: 'FREESHIPXTRA',
    title: 'Gratis Pengiriman Seluruh ID',
    minSpend: 0,
    discount: 15000,
    type: 'shipping',
    applied: true
  },
  {
    code: 'NEWDROP50K',
    title: 'Potongan Langsung Rp 50.000',
    minSpend: 400000,
    discount: 50000,
    type: 'fixed',
    applied: false
  }
];

export const shippingCouriers: ShippingOption[] = [
  {
    id: 'spx-express',
    name: 'SPX Express Standard',
    service: 'Reguler Express',
    courier: 'SPX Express',
    cost: 15000,
    etd: '1 - 2 Hari Kerja'
  },
  {
    id: 'jnt-cargo',
    name: 'J&T Super Priority',
    service: 'Next Day',
    courier: 'J&T Express',
    cost: 18000,
    etd: 'Besok Tiba (24 Jam)'
  },
  {
    id: 'sicepat-best',
    name: 'SiCepat BEST',
    service: 'Kilat Express',
    courier: 'SiCepat',
    cost: 16000,
    etd: '1 - 2 Hari'
  },
  {
    id: 'instant-courier',
    name: 'Instant Sameday (Grab / Gojek)',
    service: 'Instant 3 Jam',
    courier: 'Instant Delivery',
    cost: 32000,
    etd: 'Hari Ini (3 Jam Tiba)'
  }
];

export const paymentGateways: PaymentMethod[] = [
  {
    id: 'qris',
    name: 'QRIS Instant Pay',
    description: 'Scan otomatis via BCA, Mandiri, Gopay, OVO, Dana, ShopeePay (Bebas Biaya Admin)',
    category: 'qris'
  },
  {
    id: 'bca-va',
    name: 'BCA Virtual Account',
    description: 'Verifikasi instan otomatis 24 jam via BCA Mobile / myBCA / ATM',
    category: 'va',
    bankName: 'BCA',
    accountNumber: '8271081234567890'
  },
  {
    id: 'mandiri-va',
    name: 'Mandiri Virtual Account',
    description: 'Bayar via Livin by Mandiri atau ATM Mandiri',
    category: 'va',
    bankName: 'Mandiri',
    accountNumber: '8902208123456789'
  },
  {
    id: 'credit-card',
    name: 'Kartu Kredit / Debit (Visa & Mastercard)',
    description: 'Proteksi enkripsi 256-bit SSL & 3D Secure OTP',
    category: 'card'
  },
  {
    id: 'cod',
    name: 'COD (Bayar Tunai di Tempat)',
    description: 'Bayar tunai ke kurir saat paket sampai di alamat Anda',
    category: 'cod'
  }
];

export const defaultAddress: Address = {
  name: 'Budi Santoso',
  phone: '0812-3456-7890',
  street: 'Gedung Urban Suites Lt. 4 No. 42B, Jl. Kemang Raya',
  district: 'Mampang Prapatan',
  city: 'Jakarta Selatan',
  province: 'DKI Jakarta',
  postalCode: '12730',
  notes: 'Titip di resepsionis lobby utama jika tidak ada orang.',
  isDefault: true
};
