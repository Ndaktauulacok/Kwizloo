@extends('layouts.app', ['title' => 'Cipta Modul Pembelajaran'])

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Hero -->
    <section class="grid lg:grid-cols-[1.15fr_1fr] gap-10 lg:gap-6 items-center pt-2 pb-10">
        <div>
            <span class="sticker bg-white text-ink -rotate-2">
                <span class="w-2 h-2 rounded-full bg-tomato"></span>
                Dikuasakan oleh AI
            </span>

            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-ink leading-[1.05]">
                Biar AI ubah nota anda menjadi <span class="marker-underline">sesi pembelajaran</span> yang interaktif.
            </h1>

            <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl leading-relaxed">
                Tampal teks atau muat naik dokumen PDF. AI KwizLoo akan membaca nota anda, mengenal pasti topik utama dan menjana
                kuiz, kad imbas serta aktiviti latihan, kemudian membantu anda memberi tumpuan kepada bahagian yang memerlukan pengukuhan.
            </p>

            <ul class="mt-6 flex flex-wrap gap-x-5 gap-y-2 text-sm font-semibold text-slate-700">
                <li class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-mint border-2 border-ink flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    </span>
                    Soalan dijana oleh AI daripada nota anda
                </li>
                <li class="flex items-center gap-2">
                    <span class="w-5 h-5 rounded-md bg-sun border-2 border-ink flex items-center justify-center">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                    </span>
                    Laporan prestasi untuk guru
                </li>
            </ul>
        </div>

        <!-- Playful card pile -->
        <div class="relative h-[300px] sm:h-[340px] hidden sm:block" aria-hidden="true">
            <div class="absolute left-[6%] top-[8%] w-56 p-4 rounded-2xl bg-sky-pop border-2 border-ink shadow-pop animate-float" style="--r:-8deg; animation-delay:-1s">
                <div class="text-[11px] font-bold uppercase tracking-wider text-ink/70">Ujian Pantas</div>
                <div class="mt-1 font-display font-extrabold text-2xl text-ink">00:42</div>
                <div class="mt-3 h-2.5 rounded-full bg-white/60 border-2 border-ink overflow-hidden"><div class="h-full w-2/3 bg-ink"></div></div>
            </div>

            <div class="absolute right-[4%] top-0 w-52 p-4 rounded-2xl bg-bubblegum border-2 border-ink shadow-pop animate-float" style="--r:7deg; animation-delay:-2.5s">
                <div class="text-[11px] font-bold uppercase tracking-wider text-ink/70">Betul atau Salah?</div>
                <div class="mt-1 font-serif italic text-ink leading-snug">"Klorofil menyerap cahaya hijau."</div>
                <div class="mt-3 flex gap-2">
                    <span class="flex-1 text-center text-xs font-bold py-1 rounded-lg bg-white border-2 border-ink">Salah</span>
                    <span class="flex-1 text-center text-xs font-bold py-1 rounded-lg bg-ink text-white border-2 border-ink">Betul</span>
                </div>
            </div>

            <div class="absolute left-[24%] bottom-[2%] w-64 p-4 rounded-2xl bg-white border-2 border-ink shadow-pop-lg animate-float" style="--r:-2deg; animation-delay:-0.5s">
                <div class="flex items-center justify-between">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-tomato">Penilaian Penguasaan</span>
                    <span class="text-xs font-bold text-ink">32%</span>
                </div>
                <div class="mt-2 h-3 rounded-full bg-slate-100 border-2 border-ink overflow-hidden"><div class="h-full w-1/3 bg-tomato"></div></div>
                <div class="mt-3 text-sm font-semibold text-ink">Tindak balas cahaya</div>
                <div class="mt-2 flex items-center gap-1.5 text-xs font-bold text-mint">
                    <span class="px-2 py-0.5 rounded-md bg-ink text-sun">4 betul berturut-turut</span>
                </div>
            </div>

            <div class="absolute right-[10%] bottom-[18%] w-14 h-14 rounded-full bg-sun border-2 border-ink shadow-pop flex items-center justify-center font-display font-extrabold text-xl animate-float" style="--r:12deg; animation-delay:-3s">A+</div>
        </div>
    </section>

    <!-- Step Pipeline Banner -->
    <div class="max-w-4xl mx-auto mb-6 p-3.5 sm:p-4 rounded-2xl bg-white border-2 border-ink shadow-pop-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs font-bold text-ink">
        <div class="flex items-center gap-2 sm:gap-3 flex-wrap justify-center sm:justify-start">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-indigo-600 text-white border border-ink">
                <span class="w-2 h-2 rounded-full bg-sun"></span>
                Langkah 1: Masukkan Nota
            </span>
            <span class="text-slate-400">&rarr;</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 border border-slate-300">
                Langkah 2: AI Proses
            </span>
            <span class="text-slate-400">&rarr;</span>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-100 text-slate-500 border border-slate-300">
                Langkah 3: 9 Mod Belajar
            </span>
        </div>
        <button
            type="button"
            @click="$dispatch('open-onboarding')"
            class="inline-flex items-center gap-1.5 text-slate-700 hover:text-ink underline decoration-2 decoration-sun underline-offset-4"
        >
            <svg class="w-4 h-4 text-sun fill-sun" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8" fill="#2a1217"/></svg>
            <span>Buka Panduan Langkah Demi Langkah</span>
        </button>
    </div>

    <!-- Intake Card -->
    <section class="relative max-w-4xl mx-auto">
        <div class="absolute -top-4 left-6 z-10 sticker bg-sun text-ink rotate-[-3deg]">Mulakan di sini</div>

        <div class="academic-card p-6 sm:p-8 shadow-pop-lg" x-data="{
            isDragging: false,
            dragDepth: 0,
            justDropped: false,
            activeTab: 'paste',
            fileName: '',
            fileType: '',
            fileSizeLabel: '',
            fileError: '',
            isGenerating: false,
            handleFileSelect(files) {
                if (!files || !files[0]) return;
                const file = files[0];
                const extension = file.name.split('.').pop().toLowerCase();
                if (!['pdf', 'txt'].includes(extension)) {
                    this.clearFile();
                    this.fileError = 'Format fail mestilah PDF atau TXT sahaja.';
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    this.clearFile();
                    this.fileError = 'Saiz fail melebihi had maksimum 10MB.';
                    return;
                }
                this.fileError = '';
                this.fileName = file.name;
                this.fileType = extension.toUpperCase();
                this.fileSizeLabel = file.size >= 1024 * 1024
                    ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                    : Math.max(1, Math.round(file.size / 1024)) + ' KB';
                this.justDropped = false;
                requestAnimationFrame(() => {
                    this.justDropped = true;
                    setTimeout(() => { this.justDropped = false; }, 700);
                });
            },
            clearFile() {
                this.fileName = '';
                this.fileType = '';
                this.fileSizeLabel = '';
                this.fileError = '';
                if (this.$refs.fileInput) this.$refs.fileInput.value = '';
            },
            fillSample(title, text) {
                const titleEl = document.getElementById('title');
                const noteEl = document.getElementById('note_text');
                if (titleEl) titleEl.value = title;
                if (noteEl) noteEl.value = text;
                this.activeTab = 'paste';
            }
        }" @pageshow.window="isGenerating = false">
            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-tomato/10 border-2 border-tomato text-ink text-sm">
                    <div class="font-bold mb-1">Sila semak perkara berikut:</div>
                    <ul class="list-disc list-inside space-y-1 text-slate-700">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <h2 class="text-xl sm:text-2xl font-extrabold text-ink mb-5">Masukkan Nota Pembelajaran</h2>

            <form action="{{ route('decks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6 relative" @submit="isGenerating = true">
                @csrf

                <!-- Shown while the note is being processed -->
                <div
                    x-show="isGenerating"
                    x-transition.opacity
                    class="absolute -inset-2 z-20 rounded-2xl bg-white/90 backdrop-blur-xs flex flex-col items-center justify-center text-center gap-4"
                    role="status"
                    aria-live="polite"
                    style="display: none;"
                >
                    <div class="flex gap-2" aria-hidden="true">
                        <span class="w-3.5 h-3.5 rounded-full bg-sky-pop border-2 border-ink animate-bounce"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-sun border-2 border-ink animate-bounce [animation-delay:0.15s]"></span>
                        <span class="w-3.5 h-3.5 rounded-full bg-tomato border-2 border-ink animate-bounce [animation-delay:0.3s]"></span>
                    </div>
                    <div>
                        <p class="font-display text-xl font-extrabold text-ink">AI sedang membaca nota anda…</p>
                        <p class="mt-1 text-sm text-slate-600">Mengenal pasti topik dan menyediakan soalan. Sila tunggu sebentar.</p>
                    </div>
                </div>

                <div>
                    <label for="title" class="block text-sm font-bold text-ink mb-1.5">
                        Tajuk modul <span class="font-normal text-slate-500">(pilihan)</span>
                    </label>
                    <input
                        type="text"
                        id="title"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="cth: Sains Tingkatan 4 — Bab 2: Struktur Sel"
                        class="w-full px-4 py-3 rounded-xl border-2 border-ink bg-paper/60 focus:bg-white focus:ring-4 focus:ring-indigo-200 text-sm text-ink placeholder:text-slate-400 outline-none transition"
                    >
                </div>

                <!-- Input Tabs & Quick Preset Pills -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="inline-flex p-1 rounded-xl bg-slate-100 border-2 border-ink" role="tablist">
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'paste'"
                            @click="activeTab = 'paste'"
                            :class="activeTab === 'paste' ? 'bg-white text-ink shadow-pop-sm border-ink' : 'text-slate-600 border-transparent hover:text-ink'"
                            class="px-4 py-2 text-sm font-bold rounded-lg border-2 transition flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2"/></svg>
                            <span>Tampal teks</span>
                        </button>
                        <button
                            type="button"
                            role="tab"
                            :aria-selected="activeTab === 'upload'"
                            @click="activeTab = 'upload'"
                            :class="activeTab === 'upload' ? 'bg-white text-ink shadow-pop-sm border-ink' : 'text-slate-600 border-transparent hover:text-ink'"
                            class="px-4 py-2 text-sm font-bold rounded-lg border-2 transition flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2M12 4v12m0-12 4 4m-4-4L8 8"/></svg>
                            <span>Muat naik PDF / TXT</span>
                        </button>
                    </div>

                    <!-- Quick Sample Buttons -->
                    <div class="flex items-center gap-1.5 flex-wrap text-xs font-bold">
                        <span class="text-slate-500">Contoh segera:</span>
                        <button
                            type="button"
                            @click="fillSample('Biologi Tingkatan 4: Fotosintesis', 'Fotosintesis ialah proses tumbuhan hijau membuat makanan sendiri menggunakan karbon dioksida, air dan cahaya matahari. Hasil fotosintesis ialah glukosa dan oksigen. Karbon dioksida masuk ke dalam daun melalui stoma. Air diserap oleh akar dan diangkut ke daun melalui xilem. Proses berlaku di dalam kloroplas yang mengandungi klorofil. Glukosa berlebihan disimpan sebagai kanji.')"
                            class="px-2.5 py-1 rounded-lg bg-mint/20 hover:bg-mint/40 border border-ink text-ink transition"
                            title="Isi contoh nota Biologi"
                        >
                            Biologi
                        </button>
                        <button
                            type="button"
                            @click="fillSample('Sejarah: Kesultanan Melayu Melaka', 'Kesultanan Melayu Melaka diasaskan oleh Parameswara sekitar tahun 1400. Melaka berkembang menjadi pusat perdagangan antarabangsa kerana kedudukan strategik di Selat Melaka, terlindung daripada tiupan angin monsun barat daya dan monsun timur laut. Pentadbiran berasaskan Sistem Pembesar Empat Lipatan yang diketuai oleh Bendahara, Penghulu Bendahari, Temenggung dan Laksamana.')"
                            class="px-2.5 py-1 rounded-lg bg-sun/30 hover:bg-sun/60 border border-ink text-ink transition"
                            title="Isi contoh nota Sejarah"
                        >
                            Sejarah
                        </button>
                        <button
                            type="button"
                            @click="fillSample('Fizik: Tiga Hukum Gerakan Newton', 'Hukum Gerakan Newton Pertama menyatakan bahawa sesuatu objek akan kekal dalam keadaan pegun atau bergerak dengan halaju malar jika tiada daya luar bertindak ke atasnya (Inersia). Hukum Newton Kedua menyatakan bahawa daya paduan adalah berkadar terus dengan kadar perubahan momentum (F = ma). Hukum Newton Ketiga menyatakan bahawa setiap tindakan menghasilkan tindak balas yang sama magnitud tetapi bertentangan arah.')"
                            class="px-2.5 py-1 rounded-lg bg-sky-pop/25 hover:bg-sky-pop/50 border border-ink text-ink transition"
                            title="Isi contoh nota Fizik"
                        >
                            Fizik
                        </button>
                    </div>
                </div>

                <!-- Text Area Tab -->
                <div x-show="activeTab === 'paste'">
                    <label for="note_text" class="sr-only">Kandungan nota</label>
                    <textarea
                        id="note_text"
                        name="note_text"
                        rows="9"
                        placeholder="Tampal nota, sukatan pelajaran atau artikel di sini (sekurang-kurangnya 80 aksara)..."
                        class="w-full px-4 py-3 rounded-xl border-2 border-ink bg-paper/60 focus:bg-white focus:ring-4 focus:ring-indigo-200 text-[15px] text-ink placeholder:text-slate-400 leading-relaxed outline-none transition"
                        style="background-image: repeating-linear-gradient(transparent 0 27px, rgba(122,27,48,0.08) 27px 28px); background-attachment: local; line-height: 28px;"
                    >{{ old('note_text') }}</textarea>
                    <p class="text-xs text-slate-500 mt-2">
                        AI akan membaca nota ini untuk menjana soalan. Setiap soalan dirujuk kepada petikan asal bagi memastikan ketepatan maklumat.
                    </p>
                </div>

                <!-- File Upload Tab -->
                <div x-show="activeTab === 'upload'" style="display: none;">
                    <div
                        class="dropzone rounded-2xl bg-paper/60 px-6 py-10 text-center cursor-pointer"
                        :class="{ 'is-dragging': isDragging, 'is-dropped': justDropped, 'has-file': fileName }"
                        role="button"
                        tabindex="0"
                        aria-label="Pilih fail PDF atau TXT untuk dimuat naik"
                        @dragenter.prevent="dragDepth++; isDragging = true"
                        @dragover.prevent
                        @dragleave.prevent="dragDepth = Math.max(0, dragDepth - 1); if (dragDepth === 0) isDragging = false"
                        @drop.prevent="dragDepth = 0; isDragging = false; if ($event.dataTransfer.files.length) { $refs.fileInput.files = $event.dataTransfer.files; handleFileSelect($event.dataTransfer.files); }"
                        @click="$refs.fileInput.click()"
                        @keydown.enter.prevent="$refs.fileInput.click()"
                        @keydown.space.prevent="$refs.fileInput.click()"
                    >
                        <!-- Marching dashed border -->
                        <svg class="dropzone-border" aria-hidden="true"><rect x="1" y="1" rx="15" ry="15"/></svg>

                        <!-- Paper stack illustration -->
                        <div class="dz-icon mx-auto mb-4 w-24" aria-hidden="true">
                            <svg viewBox="0 0 96 88" class="w-24 h-[88px] overflow-visible">
                                <g class="dz-arrow">
                                    <path d="M48 2v16m-7-7 7 7 7-7" fill="none" stroke="#2a1217" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                                <g class="dz-sheet-back">
                                    <rect x="22" y="26" width="46" height="58" rx="6" fill="#efe0c8" stroke="#2a1217" stroke-width="3"/>
                                </g>
                                <g class="dz-sheet-front">
                                    <path d="M32 24h26l12 12v44a5 5 0 0 1-5 5H32a5 5 0 0 1-5-5V29a5 5 0 0 1 5-5z" fill="#ffffff" stroke="#2a1217" stroke-width="3" stroke-linejoin="round"/>
                                    <path d="M58 24v12h12" fill="none" stroke="#2a1217" stroke-width="3" stroke-linejoin="round"/>
                                    <g stroke="#d5c6b8" stroke-width="3" stroke-linecap="round">
                                        <path d="M36 46h22"/><path d="M36 54h26"/><path d="M36 62h18"/>
                                    </g>
                                    <rect x="34" y="68" width="22" height="11" rx="3" fill="#7a1b30"/>
                                    <text x="45" y="76.5" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="7.5" font-weight="800" fill="#fbf4ea" x-text="fileType || 'PDF'">PDF</text>
                                    <path class="dz-check" d="M58 66l5 5 9-11" fill="none" stroke="#2a1217" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </g>
                            </svg>
                        </div>

                        <div x-show="!fileName">
                            <p class="text-sm font-bold text-ink">
                                <span x-show="!isDragging">Seret dan lepas fail di sini, atau <span class="underline decoration-2 decoration-sun underline-offset-4">klik untuk memilih</span></span>
                                <span x-show="isDragging" style="display: none;">Lepaskan untuk memuat naik</span>
                            </p>
                            <p class="text-xs text-slate-500 mt-1">Format PDF atau TXT, saiz maksimum 10MB</p>
                        </div>

                        <div x-show="fileName" style="display: none;" class="dz-file mx-auto max-w-sm flex items-center gap-3 rounded-xl border-2 border-ink bg-white px-3 py-2.5 text-left shadow-pop-sm">
                            <span class="shrink-0 rounded-md bg-indigo-600 px-1.5 py-0.5 text-[10px] font-extrabold text-paper" x-text="fileType"></span>
                            <span class="min-w-0 flex-1">
                                <span class="block truncate text-sm font-bold text-ink" x-text="fileName"></span>
                                <span class="block text-[11px] text-slate-500 tabular-nums"><span x-text="fileSizeLabel"></span> · sedia untuk dijana</span>
                            </span>
                            <button
                                type="button"
                                @click.stop="clearFile()"
                                class="shrink-0 rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 hover:text-ink transition"
                                aria-label="Buang fail"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <p x-show="fileError" x-text="fileError" class="text-xs font-bold text-tomato mt-3" role="alert"></p>
                        <input
                            type="file"
                            name="document"
                            x-ref="fileInput"
                            @change="handleFileSelect($event.target.files)"
                            @click.stop
                            accept=".pdf,.txt,application/pdf,text/plain"
                            class="hidden"
                        >
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col-reverse sm:flex-row items-center justify-between gap-3 pt-5 border-t-2 border-dashed border-slate-200">
                    <button
                        type="submit"
                        name="use_sample"
                        value="1"
                        class="w-full sm:w-auto text-sm font-semibold text-slate-600 hover:text-ink px-2 py-2 flex items-center justify-center gap-2 underline decoration-2 decoration-sun underline-offset-4"
                    >
                        Gunakan Nota Contoh: Fotosintesis
                    </button>

                    <button type="submit" class="btn-pop w-full sm:w-auto bg-indigo-600 text-white px-7 py-3 text-base">
                        <span>Jana dengan AI</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                    </button>
                </div>
            </form>
        </div>
    </section>

    @if($recentDecks->isNotEmpty())
        <!-- Recent notes -->
        <section class="max-w-3xl mx-auto mt-12" aria-labelledby="recent-notes-heading">
            <div class="flex items-center justify-between gap-3">
                <h2 id="recent-notes-heading" class="text-lg font-extrabold text-ink">Nota terkini anda</h2>
                <a href="{{ route('decks.index') }}" class="text-sm font-bold text-indigo-600 hover:text-ink underline decoration-2 decoration-sun underline-offset-4">Lihat semua sejarah &rarr;</a>
            </div>
            <ul class="mt-3 academic-card divide-y-2 divide-dashed divide-slate-200 overflow-hidden">
                @foreach($recentDecks as $recentDeck)
                    <li>
                        <a href="{{ $recentDeck->status === 'completed' ? route('decks.show', $recentDeck) : route('decks.processing', $recentDeck) }}" class="flex items-center justify-between gap-3 px-4 py-3 hover:bg-amber-50 transition">
                            <span class="min-w-0 truncate text-sm font-bold text-ink">{{ $recentDeck->title }}</span>
                            <span class="shrink-0 text-xs text-slate-500">{{ $recentDeck->created_at?->diffForHumans() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <!-- How it works -->
    <section class="max-w-5xl mx-auto mt-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink text-center">Bagaimana KwizLoo berfungsi</h2>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="p-5 rounded-2xl bg-mint/25 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-mint border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">1</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Berasaskan nota anda</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">AI menjana soalan dan kad imbas berdasarkan kandungan nota anda sahaja, dan setiap satu disertakan petikan sumber.</p>
            </div>
            <div class="p-5 rounded-2xl bg-sun/30 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-sun border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">2</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Latihan yang menyesuaikan diri</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">Ejen AI mengesan topik yang sering dijawab salah, menjadikannya cabaran pengukuhan dan mencadangkan aktiviti seterusnya.</p>
            </div>
            <div class="p-5 rounded-2xl bg-bubblegum/30 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-bubblegum border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">3</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Laporan lengkap untuk guru</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">Ejen AI menyediakan ketepatan mengikut topik, corak kesilapan dan cadangan intervensi dalam laporan yang boleh dicetak.</p>
            </div>
        </div>
    </section>
</div>
@endsection
