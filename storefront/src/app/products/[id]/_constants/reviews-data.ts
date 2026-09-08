export interface ReviewItem {
  id: string;
  author: string;
  avatar: string;
  rating: number;
  date: string;
  variant: string;
  bodyProfile: string;
  comment: string;
  photos: string[];
  helpful: number;
}

export function getReviewsList(colorName: string, sizeName: string): ReviewItem[] {
  return [
    {
      id: 'rev-1',
      author: 'Dimas Rizky P.',
      avatar: 'D',
      rating: 5,
      date: '2 hari yang lalu',
      variant: `${colorName} • Size ${sizeName}`,
      bodyProfile: 'TB 175cm / BB 72kg (Pas & Boxy Proporsional)',
      comment:
        'Beneran tebel 300GSM padat tapi adem dipakai seharian. Jahitan kerah rib-nya kokoh banget gak gampang melar abis dicuci. Potongan bahu drop shoulder-nya beneran pas streetwear look.',
      photos: [
        'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&auto=format&fit=crop&q=80',
        'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=500&auto=format&fit=crop&q=80',
      ],
      helpful: 46,
    },
    {
      id: 'rev-2',
      author: 'Alif Fadhillah',
      avatar: 'A',
      rating: 5,
      date: '5 hari yang lalu',
      variant: 'Midnight Black • Size XL',
      bodyProfile: 'TB 180cm / BB 80kg',
      comment:
        'Warna deep black-nya pekat dan mewah banget. Kerah rib 3.5cm rapi gak kopong. Ini brand lokal kualitasnya beneran enterprise standar internasional.',
      photos: [
        'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=500&auto=format&fit=crop&q=80',
      ],
      helpful: 28,
    },
    {
      id: 'rev-3',
      author: 'Reza Kurniawan',
      avatar: 'R',
      rating: 5,
      date: '1 minggu yang lalu',
      variant: 'Onyx Black • Size M',
      bodyProfile: 'TB 168cm / BB 62kg',
      comment:
        'Packing double boxy sangat aman, pengiriman SPX cepet 1 hari sampai. Kain berat dan gak nerawang sama sekali. Pasti repeat order warna lain!',
      photos: [],
      helpful: 19,
    },
    {
      id: 'rev-4',
      author: 'Devina Putri',
      avatar: 'D',
      rating: 5,
      date: '2 minggu yang lalu',
      variant: 'Sand Khaki • Size S',
      bodyProfile: 'TB 160cm / BB 49kg (Oversized Clean Look)',
      comment:
        'Buat cewek look-nya jadi oversized keren banget. Bahannya premium tebal jatuh, jatuhnya di badan estetik.',
      photos: [],
      helpful: 12,
    },
  ];
}
