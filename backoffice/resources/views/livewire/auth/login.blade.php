<div class="w-full max-w-md">
    <!-- Brand Header -->
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#0B132B] border border-[#CBAC70]/40 shadow-2xl shadow-[#CBAC70]/10 mb-4 ring-4 ring-[#CBAC70]/10">
            <span class="font-display font-bold text-2xl text-[#CBAC70] tracking-wider">M</span>
        </div>
        <h1 class="font-display text-2xl sm:text-3xl font-bold tracking-tight text-slate-100">
            MALEGA <span class="text-[#CBAC70]">APPAREL</span>
        </h1>
        <div class="flex items-center justify-center gap-2 mt-1.5">
            <span class="h-px w-8 bg-[#CBAC70]/40"></span>
            <p class="text-xs uppercase tracking-widest text-[#CBAC70] font-semibold font-mono">Backoffice Control Portal</p>
            <span class="h-px w-8 bg-[#CBAC70]/40"></span>
        </div>
    </div>

    <!-- Login Card Container -->
    <div class="bg-[#0B132B]/90 border border-slate-800/90 rounded-3xl p-6 sm:p-8 shadow-2xl backdrop-blur-xl relative overflow-hidden">
        <!-- Accent Top Stitch Line -->
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-transparent via-[#CBAC70] to-transparent opacity-75"></div>

        <!-- Session Status Flash Messages -->
        @if (session('status'))
            <div class="mb-6">
                <x-alert type="info" dismissible="true">
                    {{ session('status') }}
                </x-alert>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-6">
                <x-alert type="error" dismissible="true">
                    {{ session('error') }}
                </x-alert>
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            <!-- Email Input -->
            <x-input
                wire:model="form.email"
                label="Alamat Email Staf"
                name="form.email"
                type="email"
                placeholder="nama@malega.id"
                required="true"
                autocomplete="email"
                autofocus
            />

            <!-- Password Input -->
            <div class="space-y-1.5">
                <div class="flex items-center justify-between">
                    <label for="form.password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Kata Sandi <span class="text-rose-400 font-mono">*</span>
                    </label>
                </div>
                
                <x-input
                    wire:model="form.password"
                    name="form.password"
                    type="password"
                    placeholder="••••••••"
                    required="true"
                    autocomplete="current-password"
                />
            </div>

            <!-- Remember Me & Security Badge -->
            <div class="flex items-center justify-between pt-1">
                <label class="flex items-center gap-2 text-xs text-slate-400 hover:text-slate-200 cursor-pointer select-none">
                    <input
                        type="checkbox"
                        wire:model="form.remember"
                        class="w-4 h-4 rounded bg-[#070C1A] border-slate-700 text-[#CBAC70] focus:ring-[#CBAC70]/40 focus:ring-offset-0 focus:ring-1 transition-colors"
                    >
                    <span>Ingat sesi saya</span>
                </label>

                <span class="inline-flex items-center gap-1 text-[11px] font-mono text-slate-500">
                    <svg class="w-3.5 h-3.5 text-[#CBAC70]/70" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                    256-bit SSL
                </span>
            </div>

            <!-- Submit Button with 5 UI States Handling (Guaranteed Single Row) -->
            <div class="pt-3">
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full relative group overflow-hidden rounded-xl bg-gradient-to-r from-[#CBAC70] to-[#BD9B58] hover:from-[#DFB67A] hover:to-[#CBAC70] text-[#0B132B] font-bold text-sm py-3.5 px-4 shadow-lg shadow-[#CBAC70]/10 focus:outline-none focus:ring-2 focus:ring-[#CBAC70] focus:ring-offset-2 focus:ring-offset-[#0B132B] transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer active:scale-[0.99] flex items-center justify-center gap-2 whitespace-nowrap"
                >
                    <!-- Normal State Icon & Label -->
                    <span wire:loading.remove wire:target="login" class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
                        <span>Masuk ke Sistem</span>
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </span>

                    <!-- Loading State Spinner & Label (Single-row inline-flex) -->
                    <span wire:loading.inline-flex wire:target="login" class="items-center justify-center gap-2.5 whitespace-nowrap">
                        <svg class="animate-spin h-4 w-4 text-[#0B132B] shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        <span>Memverifikasi Kredensial...</span>
                    </span>
                </button>
            </div>
        </form>

        <!-- Isolation Notice -->
        <div class="mt-6 pt-5 border-t border-slate-800/80 text-center">
            <p class="text-[11px] text-slate-500 flex items-center justify-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-amber-500/70 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Akses terbatas hanya untuk staf & operator resmi Malega Apparel.</span>
            </p>
        </div>
    </div>
</div>
