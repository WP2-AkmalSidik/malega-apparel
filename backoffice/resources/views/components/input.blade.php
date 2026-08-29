@props([
    'label' => null,
    'name' => null,
    'type' => 'text',
    'placeholder' => '',
    'required' => false,
    'hint' => null,
    'error' => null,
])

@php
$hasError = $error || ($name && $errors->has($name));
$errorMessage = $error ?? ($name ? $errors->first($name) : null);
@endphp

<div class="w-full space-y-1.5">
    @if($label)
        <label @if($name) for="{{ $name }}" @endif class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-rose-400 font-mono">*</span>
            @endif
        </label>
    @endif

    <div class="relative rounded-xl shadow-inner">
        <input
            type="{{ $type }}"
            @if($name) id="{{ $name }}" name="{{ $name }}" @endif
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full rounded-xl bg-[#0B132B]/70 border ' . 
                    ($hasError 
                        ? 'border-rose-500/80 text-rose-100 placeholder-rose-400/50 focus:border-rose-500 focus:ring-rose-500/30' 
                        : 'border-slate-700/80 text-slate-100 placeholder-slate-500 focus:border-[#CBAC70] focus:ring-[#CBAC70]/30') . 
                    ' px-4 py-3 text-sm transition-colors duration-150 focus:outline-none focus:ring-2 backdrop-blur-sm'
            ]) }}
        />

        @if($hasError)
            <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center pointer-events-none text-rose-400">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-5a.75.75 0 01.75.75v4.5a.75.75 0 01-1.5 0v-4.5A.75.75 0 0110 5zm0 10a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                </svg>
            </div>
        @endif
    </div>

    @if($errorMessage)
        <p class="text-xs text-rose-400 flex items-center gap-1 mt-1 font-medium">
            <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $errorMessage }}</span>
        </p>
    @elseif($hint)
        <p class="text-xs text-slate-400 mt-1">{{ $hint }}</p>
    @endif
</div>
