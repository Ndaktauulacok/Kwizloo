<div
    x-data="{
        open: false,
        step: 1,
        totalSteps: 3,
        init() {
            // Auto open on first visit if not seen
            const seen = localStorage.getItem('kwizloo_onboarding_v1');
            if (!seen && (window.location.pathname === '/' || window.location.pathname.includes('/decks/create'))) {
                setTimeout(() => {
                    this.open = true;
                }, 600);
            }
        },
        openTour() {
            this.step = 1;
            this.open = true;
        },
        closeTour(markSeen = true) {
            this.open = false;
            if (markSeen) {
                localStorage.setItem('kwizloo_onboarding_v1', 'true');
            }
        },
        nextStep() {
            if (this.step < this.totalSteps) {
                this.step++;
            } else {
                this.closeTour(true);
            }
        },
        prevStep() {
            if (this.step > 1) {
                this.step--;
            }
        }
    }"
    @open-onboarding.window="openTour()"
    @keydown.escape.window="if (open) closeTour(true)"
    x-cloak
>
    <!-- Modal Backdrop & Container -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-ink/70 backdrop-blur-xs flex items-center justify-center p-3 sm:p-5"
        role="dialog"
        aria-modal="true"
        aria-labelledby="tour-modal-title"
        style="display: none;"
    >
        <!-- Modal Card -->
        <div
            @click.outside="closeTour(true)"
            x-show="open"
            x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="opacity-0 scale-95 translate-y-3"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100 translate-y-0"
            x-transition:leave-end="opacity-0 scale-95 translate-y-3"
            class="relative w-full max-w-2xl bg-white border-3 border-ink rounded-3xl shadow-pop-lg overflow-hidden flex flex-col"
        >
            <!-- Top Bar with Stepper & Close -->
            <div class="px-5 sm:px-7 py-4 border-b-2 border-ink bg-paper/70 flex items-center justify-between gap-4">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full bg-tomato border-2 border-ink"></span>
                    <span class="font-display font-extrabold text-xs uppercase tracking-wider text-ink">
                        Panduan Pengguna KwizLoo
                    </span>
                    <span class="px-2 py-0.5 rounded-full bg-sun border-2 border-ink text-[11px] font-black text-ink">
                        Langkah <span x-text="step"></span>/3
                    </span>
                </div>

                <button
                    type="button"
                    @click="closeTour(true)"
                    class="w-8 h-8 rounded-xl bg-white border-2 border-ink shadow-pop-sm flex items-center justify-center text-ink font-extrabold hover:bg-slate-100 transition active:translate-x-0.5 active:translate-y-0.5"
                    aria-label="Tutup panduan"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-100 h-2 border-b-2 border-ink">
                <div
                    class="bg-indigo-600 h-full transition-all duration-300 ease-out"
                    :style="`width: ${(step / totalSteps) * 100}%`"
                ></div>
            </div>

            <!-- Modal Body (Steps) -->
            <div class="p-6 sm:p-8 flex-1 min-h-[380px] flex flex-col justify-between">
                <!-- STEP 1: Muat Naik / Tampal Nota -->
                <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-12 h-12 rounded-2xl bg-sky-pop border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl text-ink">
                            1
                        </span>
                        <div>
                            <span class="sticker bg-sky-pop/30 text-ink text-xs mb-2">Langkah Pertama</span>
                            <h2 id="tour-modal-title" class="text-2xl sm:text-3xl font-extrabold text-ink leading-tight">
                                Masukkan Nota atau Dokumen PDF
                            </h2>
                            <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                                Anda hanya perlu menyediakan nota atau bahan bacaan. Salin teks nota anda atau seret fail PDF terus ke dalam KwizLoo.
                            </p>
                        </div>
                    </div>

                    <!-- Step 1 Interactive Graphic Mockup -->
                    <div class="mt-6 p-4 rounded-2xl bg-paper border-2 border-ink shadow-pop-sm">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-600 mb-2">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Contoh Input Nota
                            </span>
                            <span class="px-2 py-0.5 rounded-md bg-white border border-ink text-slate-700">PDF / Teks</span>
                        </div>
                        <div class="p-3 rounded-xl bg-white border-2 border-ink text-xs font-serif italic text-slate-700 leading-relaxed">
                            "Fotosintesis ialah proses tumbuhan hijau membuat makanan sendiri menggunakan karbon dioksida, air dan cahaya matahari. Hasilnya ialah glukosa dan oksigen..."
                        </div>
                        <div class="mt-3 flex flex-wrap gap-2 text-[11px] font-bold text-slate-700">
                            <span class="px-2 py-1 rounded-lg bg-mint/30 border border-ink">✓ Tanpa format rumit</span>
                            <span class="px-2 py-1 rounded-lg bg-sun/30 border border-ink">✓ Sesuai untuk semua subjek</span>
                            <span class="px-2 py-1 rounded-lg bg-bubblegum/30 border border-ink">✓ Sokong nota ringkas & buku teks</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 2: AI Proses & Jana 9 Permainan -->
                <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-12 h-12 rounded-2xl bg-sun border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl text-ink">
                            2
                        </span>
                        <div>
                            <span class="sticker bg-sun/30 text-ink text-xs mb-2">Langkah Kedua</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink leading-tight">
                                AI Menjana Soalan & 9 Mod Latihan
                            </h2>
                            <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                                Dalam masa beberapa saat, AI pintar membaca nota anda, membahagikannya kepada topik penting, dan menghasilkan pelbagai jenis aktiviti secara automatik.
                            </p>
                        </div>
                    </div>

                    <!-- Step 2 Interactive Graphic Mockup -->
                    <div class="mt-6 p-4 rounded-2xl bg-white border-2 border-ink shadow-pop-sm space-y-3">
                        <div class="flex items-center justify-between text-xs font-bold">
                            <span class="text-indigo-600 flex items-center gap-1">
                                <svg class="w-4 h-4 animate-spin text-indigo-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                AI Menjana Automatik
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-mint text-ink font-black text-[10px]">100% Daripada Nota Anda</span>
                        </div>

                        <!-- Generated items pills -->
                        <div class="grid grid-cols-2 gap-2 text-xs font-bold">
                            <div class="p-2.5 rounded-xl bg-paper border border-ink flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-tomato text-white flex items-center justify-center text-[10px] font-extrabold">B</span>
                                <div>
                                    <div class="text-ink">Cabaran Bos</div>
                                    <div class="text-[10px] text-slate-500 font-normal">Aras pemikiran tinggi</div>
                                </div>
                            </div>
                            <div class="p-2.5 rounded-xl bg-paper border border-ink flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-sun text-ink flex items-center justify-center text-[10px] font-extrabold">60</span>
                                <div>
                                    <div class="text-ink">Ujian Pantas 60s</div>
                                    <div class="text-[10px] text-slate-500 font-normal">Latih refleks pantas</div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-300 text-xs text-slate-700 flex items-center justify-between">
                            <span class="font-medium">Semua soalan disokong petikan rujukan nota asal untuk jaminan ketepatan.</span>
                            <span class="text-mint font-extrabold text-sm ml-2">✓ Sahih</span>
                        </div>
                    </div>
                </div>

                <!-- STEP 3: Belajar, Takluk Bos & Semak Laporan -->
                <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">
                    <div class="flex items-start gap-4">
                        <span class="shrink-0 w-12 h-12 rounded-2xl bg-mint border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl text-ink">
                            3
                        </span>
                        <div>
                            <span class="sticker bg-mint/30 text-ink text-xs mb-2">Langkah Ketiga</span>
                            <h2 class="text-2xl sm:text-3xl font-extrabold text-ink leading-tight">
                                Belajar, Takluk Topik Lemah & Pantau Kemajuan
                            </h2>
                            <p class="mt-2 text-sm sm:text-base text-slate-600 leading-relaxed">
                                Pilih mod kegemaran anda. Jika anda kerap salah pada topik tertentu, AI akan membimbing anda sehingga topik tersebut dikuasai sepenuhnya!
                            </p>
                        </div>
                    </div>

                    <!-- Step 3 Interactive Graphic Mockup -->
                    <div class="mt-6 p-4 rounded-2xl bg-paper border-2 border-ink shadow-pop-sm space-y-3">
                        <div class="p-3 rounded-xl bg-ink text-white flex items-center justify-between">
                            <div>
                                <div class="text-xs font-bold text-sun uppercase tracking-wider">Ejen Pembelajaran Adaptif</div>
                                <div class="text-sm font-extrabold">Mengesan topik lemah secara masa nyata</div>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg bg-tomato text-white text-xs font-black animate-wiggle">
                                Bos Aktif
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1 text-xs font-bold text-slate-700">
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-mint"></span> 9 Mod Interaktif
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-sun"></span> Bantuan Klu AI
                            </span>
                            <span class="flex items-center gap-1">
                                <span class="w-2.5 h-2.5 rounded-full bg-sky-pop"></span> Laporan Guru & Prestasi
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Footer Navigation Buttons -->
                <div class="mt-8 pt-5 border-t-2 border-dashed border-slate-200 flex flex-col-reverse sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-between sm:justify-start">
                        <button
                            type="button"
                            @click="closeTour(true)"
                            class="text-xs font-bold text-slate-500 hover:text-ink underline decoration-2 decoration-slate-300 hover:decoration-ink underline-offset-4 py-1"
                        >
                            Langkau Panduan
                        </button>

                        <!-- Step indicator dots -->
                        <div class="flex items-center gap-1.5 ml-4" aria-label="Penunjuk langkah">
                            <button
                                type="button"
                                @click="step = 1"
                                class="w-3 h-3 rounded-full border-2 border-ink transition-transform"
                                :class="step === 1 ? 'bg-indigo-600 scale-125' : 'bg-slate-200 hover:bg-slate-300'"
                                aria-label="Ke langkah 1"
                            ></button>
                            <button
                                type="button"
                                @click="step = 2"
                                class="w-3 h-3 rounded-full border-2 border-ink transition-transform"
                                :class="step === 2 ? 'bg-indigo-600 scale-125' : 'bg-slate-200 hover:bg-slate-300'"
                                aria-label="Ke langkah 2"
                            ></button>
                            <button
                                type="button"
                                @click="step = 3"
                                class="w-3 h-3 rounded-full border-2 border-ink transition-transform"
                                :class="step === 3 ? 'bg-indigo-600 scale-125' : 'bg-slate-200 hover:bg-slate-300'"
                                aria-label="Ke langkah 3"
                            ></button>
                        </div>
                    </div>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                        <button
                            type="button"
                            x-show="step > 1"
                            @click="prevStep()"
                            class="btn-pop bg-white text-ink text-sm px-4 py-2"
                        >
                            Sebelumnya
                        </button>

                        <button
                            type="button"
                            x-show="step < 3"
                            @click="nextStep()"
                            class="btn-pop bg-indigo-600 text-white text-sm px-6 py-2 group"
                        >
                            <span>Seterusnya</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                        </button>

                        <a
                            x-show="step === 3"
                            href="{{ route('decks.create') }}"
                            @click="closeTour(true)"
                            class="btn-pop bg-sun text-ink text-sm px-6 py-2 font-extrabold group"
                        >
                            <span>Mula Cipta Modul Sekarang</span>
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
