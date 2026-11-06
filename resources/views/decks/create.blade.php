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

    <!-- Intake Card -->
    <section class="relative max-w-4xl mx-auto">
        <div class="absolute -top-4 left-6 z-10 sticker bg-sun text-ink rotate-[-3deg]">Mulakan di sini</div>

        <div class="academic-card p-6 sm:p-8 shadow-pop-lg" x-data="{ isDragging: false, activeTab: 'paste', fileName: '', isGenerating: false }" @pageshow.window="isGenerating = false">
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

                <!-- Input Tabs -->
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

                <!-- Text Area Tab -->
                <div x-show="activeTab === 'paste'">
                    <label for="note_text" class="sr-only">Kandungan nota</label>
                    <textarea
                        id="note_text"
                        name="note_text"
                        rows="9"
                        placeholder="Tampal nota, sukatan pelajaran atau artikel di sini (sekurang-kurangnya 80 aksara)..."
                        class="w-full px-4 py-3 rounded-xl border-2 border-ink bg-paper/60 focus:bg-white focus:ring-4 focus:ring-indigo-200 text-[15px] text-ink placeholder:text-slate-400 leading-relaxed outline-none transition"
                        style="background-image: repeating-linear-gradient(transparent 0 27px, rgba(33,72,232,0.08) 27px 28px); background-attachment: local; line-height: 28px;"
                    >{{ old('note_text') }}</textarea>
                    <p class="text-xs text-slate-500 mt-2">
                        AI akan membaca nota ini untuk menjana soalan. Setiap soalan dirujuk kepada petikan asal bagi memastikan ketepatan maklumat.
                    </p>
                </div>

                <!-- File Upload Tab -->
                <div x-show="activeTab === 'upload'" style="display: none;">
                    <div
                        class="border-2 border-dashed rounded-2xl p-10 text-center transition cursor-pointer"
                        :class="isDragging ? 'border-indigo-600 bg-indigo-50' : 'border-ink/40 bg-paper/60 hover:border-ink hover:bg-white'"
                        @dragover.prevent="isDragging = true"
                        @dragleave.prevent="isDragging = false"
                        @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; fileName = $event.dataTransfer.files[0]?.name || ''"
                        @click="$refs.fileInput.click()"
                    >
                        <div class="mx-auto w-14 h-14 rounded-2xl bg-sky-pop border-2 border-ink shadow-pop-sm flex items-center justify-center mb-3 rotate-3">
                            <svg class="w-7 h-7 text-ink" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5zM14 3v5h5M12 12v6m-3-3h6"/></svg>
                        </div>
                        <p class="text-sm font-bold text-ink" x-text="fileName || 'Klik untuk memilih fail, atau seret dan lepas di sini'"></p>
                        <p class="text-xs text-slate-500 mt-1">Format PDF atau TXT, saiz maksimum 10MB</p>
                        <input
                            type="file"
                            name="document"
                            x-ref="fileInput"
                            @change="fileName = $event.target.files[0]?.name || ''"
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
