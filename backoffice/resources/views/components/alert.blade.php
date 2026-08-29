@props([
    'type' => 'info',
    'dismissible' => false,
])

@php
$typeClasses = match ($type) {
    'success' => 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300',
    'warning' => 'bg-amber-950/40 border-amber-500/40 text-amber-300',
    'error', 'danger' => 'bg-rose-950/40 border-rose-500/40 text-rose-300',
    default => 'bg-sky-950/40 border-sky-500/40 text-sky-300',
};

$icon = match ($type) {
    'success' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
    'warning' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />',
    'error', 'danger' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
    default => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />',
};
@endphp

<div
    x-data="{ show: true }"
    x-show="show"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    {{ $attributes->merge(['class' => "p-4 rounded-xl border flex items-start gap-3 backdrop-blur-sm {$typeClasses}"]) }}
>
    <svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        {!! $icon !!}
    </svg>

    <div class="flex-1 text-sm font-medium leading-relaxed">
        {{ $slot }}
    </div>

    @if($dismissible)
        <button
            type="button"
            @click="show = false"
            class="text-current opacity-70 hover:opacity-100 transition-opacity p-0.5 -mr-1"
        >
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    @endif
</div>
