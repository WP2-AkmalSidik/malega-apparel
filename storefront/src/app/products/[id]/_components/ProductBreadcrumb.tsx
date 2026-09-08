import React from 'react';
import Link from 'next/link';
import { ChevronRight } from 'lucide-react';

interface ProductBreadcrumbProps {
  title: string;
}

export default function ProductBreadcrumb({ title }: ProductBreadcrumbProps) {
  return (
    <nav className="flex items-center gap-2 text-xs font-mono text-[#94A3B8]">
      <Link href="/" className="hover:text-[#CBAC70] transition-colors">
        HOME
      </Link>
      <ChevronRight className="w-3.5 h-3.5" />
      <span className="text-[#CBAC70] uppercase font-bold truncate max-w-xs">{title}</span>
    </nav>
  );
}
