@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'target' => null,
    'icon' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-xl transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-[#0B132B] disabled:opacity-50 disabled:cursor-not-allowed disabled:pointer-events-none cursor-pointer';

$sizeClasses = match ($size) {
    'sm' => 'px-3 py-1.5 text-xs gap-1.5',
    'lg' => 'px-6 py-3.5 text-base gap-2.5 shadow-lg',
    default => 'px-4 py-2.5 text-sm gap-2 shadow-sm',
};

$variantClasses = match ($variant) {
    'gold' => 'bg-[#CBAC70] hover:bg-[#BD9B58] text-[#0B132B] font-semibold focus:ring-[#CBAC70] shadow-amber-950/20 active:scale-[0.98]',
    'secondary' => 'bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700/80 focus:ring-slate-500 active:scale-[0.98]',
    'danger' => 'bg-rose-600 hover:bg-rose-700 text-white focus:ring-rose-500 shadow-rose-950/20 active:scale-[0.98]',
    'ghost' => 'bg-transparent hover:bg-slate-800/60 text-slate-300 hover:text-white focus:ring-slate-600',
    default => 'bg-[#0B132B] hover:bg-[#121B3D] text-white border border-[#CBAC70]/40 hover:border-[#CBAC70] focus:ring-[#CBAC70] shadow-[#0B132B]/40 active:scale-[0.98]',
};
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "{$baseClasses} {$sizeClasses} {$variantClasses}"]) }}
    @if($target)
        wire:target="{{ $target }}"
        wire:loading.attr="disabled"
    @endif
>
    @if($target)
        <svg wire:loading wire:target="{{ $target }}" class="animate-spin -ml-0.5 h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
        </svg>
    @endif

    {{ $slot }}
</button>
