@props([
    'variant' => 'neutral',
    'size' => 'md',
])

@php
$sizeClasses = match ($size) {
    'sm' => 'px-2 py-0.5 text-[10px]',
    'lg' => 'px-3 py-1 text-sm',
    default => 'px-2.5 py-1 text-xs',
};

$variantClasses = match ($variant) {
    'gold' => 'bg-[#CBAC70]/15 text-[#CBAC70] border border-[#CBAC70]/30',
    'navy' => 'bg-[#0B132B] text-slate-200 border border-slate-700',
    'success' => 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30',
    'warning' => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
    'danger' => 'bg-rose-500/15 text-rose-400 border border-rose-500/30',
    'info' => 'bg-sky-500/15 text-sky-400 border border-sky-500/30',
    default => 'bg-slate-800 text-slate-300 border border-slate-700/60',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center gap-1.5 font-medium rounded-lg {$sizeClasses} {$variantClasses}"]) }}>
    {{ $slot }}
</span>
