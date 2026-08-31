<div
    x-data="{
        toasts: [],
        init() {
            @if(session('success'))
                this.addToast({ type: 'success', message: '{{ session('success') }}', title: 'Berhasil' });
            @endif
            @if(session('status'))
                this.addToast({ type: 'info', message: '{{ session('status') }}', title: 'Informasi' });
            @endif
            @if(session('error'))
                this.addToast({ type: 'error', message: '{{ session('error') }}', title: 'Terjadi Kesalahan' });
            @endif
            @if(session('warning'))
                this.addToast({ type: 'warning', message: '{{ session('warning') }}', title: 'Peringatan' });
            @endif
        },
        addToast(raw) {
            // Robustly unwrap payload from Livewire 3/4 ($event.detail can be Array, indexed Object, or direct Object/String)
            let toast = raw;
            if (Array.isArray(raw) && raw.length > 0) {
                toast = raw[0];
            } else if (raw && typeof raw === 'object' && raw[0]) {
                toast = raw[0];
            }

            if (typeof toast === 'string') {
                toast = { message: toast };
            }

            toast = toast || {};

            const id = Date.now() + Math.random().toString(36).substr(2, 9);
            const type = toast.type || 'info';
            const title = toast.title || (type === 'success' ? 'Berhasil' : (type === 'error' || type === 'danger' ? 'Gagal' : (type === 'warning' ? 'Peringatan' : 'Pemberitahuan')));
            const message = toast.message || '';

            const newToast = {
                id,
                type,
                title,
                message,
                visible: true,
                timeout: toast.timeout || 4000
            };
            this.toasts.push(newToast);

            setTimeout(() => {
                this.dismissToast(id);
            }, newToast.timeout);
        },
        dismissToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast && toast.visible) {
                toast.visible = false;
                // Wait for exit animation to finish before removing from array
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        }
    }"
    x-on:toast.window="addToast($event.detail)"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"
    style="display: none;"
    x-show="toasts.length > 0"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-350 transform"
            x-transition:enter-start="translate-x-[110%] opacity-0"
            x-transition:enter-end="translate-x-0 opacity-100"
            x-transition:leave="transition ease-in duration-250 transform"
            x-transition:leave-start="translate-y-0 opacity-100 scale-100"
            x-transition:leave-end="translate-y-10 opacity-0 scale-95"
            class="pointer-events-auto w-full overflow-hidden rounded-2xl bg-[#0B132B]/95 border shadow-2xl backdrop-blur-xl transition-all duration-200"
            :class="{
                'border-emerald-500/40 shadow-emerald-950/20': toast.type === 'success',
                'border-rose-500/40 shadow-rose-950/20': toast.type === 'error' || toast.type === 'danger',
                'border-amber-500/40 shadow-amber-950/20': toast.type === 'warning',
                'border-[#CBAC70]/40 shadow-gold/10': toast.type === 'info' || toast.type === 'gold'
            }"
        >
            <div class="p-4 flex items-start gap-3 relative">
                <!-- Glowing Indicator Strip on Left -->
                <div
                    class="absolute left-0 inset-y-0 w-1.5"
                    :class="{
                        'bg-emerald-400 shadow-[0_0_10px_rgba(52,211,153,0.8)]': toast.type === 'success',
                        'bg-rose-400 shadow-[0_0_10px_rgba(251,113,133,0.8)]': toast.type === 'error' || toast.type === 'danger',
                        'bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.8)]': toast.type === 'warning',
                        'bg-[#CBAC70] shadow-[0_0_10px_rgba(203,172,112,0.8)]': toast.type === 'info' || toast.type === 'gold'
                    }"
                ></div>

                <!-- Toast Icon -->
                <div
                    class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 ml-1"
                    :class="{
                        'bg-emerald-500/15 text-emerald-400': toast.type === 'success',
                        'bg-rose-500/15 text-rose-400': toast.type === 'error' || toast.type === 'danger',
                        'bg-amber-500/15 text-amber-400': toast.type === 'warning',
                        'bg-[#CBAC70]/15 text-[#CBAC70]': toast.type === 'info' || toast.type === 'gold'
                    }"
                >
                    <!-- Success Icon -->
                    <template x-if="toast.type === 'success'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                    </template>
                    <!-- Error Icon -->
                    <template x-if="toast.type === 'error' || toast.type === 'danger'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </template>
                    <!-- Warning Icon -->
                    <template x-if="toast.type === 'warning'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </template>
                    <!-- Info Icon -->
                    <template x-if="toast.type === 'info' || toast.type === 'gold'">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </template>
                </div>

                <!-- Toast Text Content -->
                <div class="flex-1 min-w-0 pr-1">
                    <p class="text-xs font-semibold text-slate-100 leading-snug" x-text="toast.title"></p>
                    <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed break-words" x-text="toast.message"></p>
                </div>

                <!-- Dismiss Button -->
                <button
                    type="button"
                    @click="dismissToast(toast.id)"
                    class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-800 transition-colors cursor-pointer shrink-0"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </template>
</div>
