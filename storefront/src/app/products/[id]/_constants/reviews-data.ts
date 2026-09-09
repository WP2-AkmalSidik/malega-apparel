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

export function getReviewsList(colorName?: string, sizeName?: string): ReviewItem[] {
  return [];
}
