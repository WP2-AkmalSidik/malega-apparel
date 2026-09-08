/**
 * Shared utility functions used across multiple storefront pages.
 * Extracted to eliminate duplication in track, checkout, and order-confirmation.
 */

/**
 * Format a number as Indonesian Rupiah currency string.
 * @example formatRupiah(189000) => "Rp 189.000"
 */
export function formatRupiah(val: number): string {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    maximumFractionDigits: 0,
  }).format(val);
}

/**
 * Copy text to clipboard using the Clipboard API.
 * Returns true if successful, false otherwise.
 */
export async function copyToClipboard(text: string): Promise<boolean> {
  try {
    await navigator.clipboard.writeText(text);
    return true;
  } catch {
    return false;
  }
}

/**
 * Format a date string or Date object to Indonesian locale string.
 * @example formatDateID('2026-09-04T06:00:00Z') => "4 Sep 2026 13:00 WIB"
 */
export function formatDateID(
  date: string | Date,
  options?: { includeTime?: boolean }
): string {
  const d = typeof date === 'string' ? new Date(date) : date;
  const opts: Intl.DateTimeFormatOptions = {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  };

  if (options?.includeTime !== false) {
    opts.hour = '2-digit';
    opts.minute = '2-digit';
  }

  return d.toLocaleString('id-ID', opts) + (options?.includeTime !== false ? ' WIB' : '');
}
