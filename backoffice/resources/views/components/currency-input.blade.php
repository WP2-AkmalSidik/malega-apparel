@props([
    'label' => null,
    'name' => null,
    'placeholder' => '0',
    'prefix' => 'Rp',
    'required' => false,
    'hint' => null,
    'error' => null,
    'allowNull' => true,
])

@php
$wireModel = $attributes->wire('model')->value() ?? $name;
$hasError = $error || ($wireModel && $errors->has($wireModel));
$errorMessage = $error ?? ($wireModel ? $errors->first($wireModel) : null);
@endphp

<div class="space-y-1">
    @if($label)
        <label class="block text-[10px] font-mono font-bold uppercase tracking-wider text-slate-300">
            {{ $label }}
            @if($required)
                <span class="text-rose-400">*</span>
            @endif
        </label>
    @endif

    <div
        x-data="{
            display: '',
            init() {
                this.updateDisplay($wire.get('{{ $wireModel }}'));
                this.$watch('$wire.{{ $wireModel }}', val => this.updateDisplay(val));
            },
            updateDisplay(val) {
                if (val === null || val === undefined || val === '') {
                    this.display = '';
                    return;
                }
                let raw = String(val).replace(/\D/g, '');
                this.display = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
            },
            formatVal(e) {
                let raw = e.target.value.replace(/\D/g, '');
                if (!raw) {
                    this.display = '';
                    @if($allowNull)
                        $wire.set('{{ $wireModel }}', null);
                    @else
                        $wire.set('{{ $wireModel }}', 0);
                    @endif
                    return;
                }
                this.display = new Intl.NumberFormat('id-ID').format(raw);
                $wire.set('{{ $wireModel }}', parseInt(raw, 10));
            }
        }"
        class="relative"
    >
        @if($prefix)
            <span class="absolute left-2.5 top-2.5 text-[11px] text-[#CBAC70] font-mono font-bold select-none pointer-events-none">{{ $prefix }}</span>
        @endif

        <input
            type="text"
            x-model="display"
            x-on:input="formatVal($event)"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'w-full h-9 font-mono bg-[#070C1A] border ' . 
                    ($hasError 
                        ? 'border-rose-500 text-rose-200 placeholder-rose-400/50 focus:border-rose-500' 
                        : 'border-slate-700/80 text-slate-200 placeholder:text-slate-500 focus:border-[#CBAC70]') . 
                    ' rounded-lg ' . ($prefix ? 'pl-8' : 'px-2.5') . ' pr-2.5 text-xs focus:outline-none transition-colors'
            ]) }}
        />
    </div>

    @if($errorMessage)
        <p class="text-[10px] text-rose-400">{{ $errorMessage }}</p>
    @elseif($hint)
        <span class="text-[10px] text-slate-500 block">{{ $hint }}</span>
    @endif
</div>
