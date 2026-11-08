@extends('layouts.app', ['title' => 'Cara Ia Berfungsi'])

@section('content')
<div class="max-w-5xl mx-auto">
    <a href="{{ route('home') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-slate-600 hover:text-ink">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 12H5m6-6-6 6 6 6"/></svg>
        Kembali ke halaman utama
    </a>

    <!-- Intro -->
    <header class="mt-6 text-center">
        <span class="sticker bg-white text-ink -rotate-2">
            <span class="w-2 h-2 rounded-full bg-mint"></span>
            Dikuasakan oleh AI
        </span>
        <h1 class="mt-5 text-3xl sm:text-5xl font-extrabold text-ink leading-tight">
            Cara KwizLoo <span class="marker-underline">berfungsi</span>
        </h1>
        <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            Anda hanya perlu menyediakan nota. AI KwizLoo akan melakukan selebihnya, dalam tiga langkah mudah.
        </p>

        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            <button
                type="button"
                @click="$dispatch('open-onboarding')"
                class="btn-pop bg-sun text-ink text-sm px-5 py-2.5 rounded-xl flex items-center gap-2 font-extrabold"
            >
                <svg class="w-4 h-4 fill-ink" viewBox="0 0 24 24"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                <span>Buka Panduan Langkah Demi Langkah</span>
            </button>
            <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-sm px-5 py-2.5 rounded-xl">
                Cipta Modul Sekarang &rarr;
            </a>
        </div>
    </header>

    <!-- Steps -->
    <ol class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-5">
        <li class="relative p-6 rounded-2xl bg-sky-pop/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-sky-pop border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">1</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">Masukkan nota</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                Tampal teks atau muat naik dokumen PDF. AI akan membaca keseluruhan kandungan dan mengenal pasti topik-topik utama.
            </p>
        </li>
        <li class="relative p-6 rounded-2xl bg-sun/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-sun border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">2</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">AI menjana bahan latihan</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                AI menyediakan soalan, pilihan jawapan, kad imbas dan penjelasan berdasarkan nota anda, lengkap dengan petikan sumber.
            </p>
        </li>
        <li class="relative p-6 rounded-2xl bg-mint/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-mint border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">3</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">Belajar bersama ejen AI</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                Ejen AI menganalisis jawapan anda, mengesan topik yang masih lemah dan mencadangkan aktiviti seterusnya yang paling sesuai.
            </p>
        </li>
    </ol>

    <!-- What the AI does -->
    <section class="mt-16 grid lg:grid-cols-[1fr_1.2fr] gap-8 items-center">
        <div>
            <span class="sticker bg-sun text-ink rotate-1">Di sebalik tabir</span>
            <h2 class="mt-4 text-2xl sm:text-3xl font-extrabold text-ink">Apa yang AI lakukan untuk anda?</h2>
            <p class="mt-3 text-slate-600 leading-relaxed">
                Kerja yang biasanya mengambil masa berjam-jam, seperti membaca semula nota, menyediakan soalan dan menyemak
                jawapan, kini dilakukan oleh AI dalam beberapa saat. Anda boleh terus fokus untuk belajar.
            </p>
            <p class="mt-4 text-sm text-slate-500 leading-relaxed">
                Setiap soalan disertakan petikan daripada nota asal supaya anda sentiasa boleh menyemak ketepatannya.
            </p>
        </div>
        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <li class="p-4 rounded-xl bg-white border-2 border-ink shadow-pop-sm">
                <span class="inline-flex w-9 h-9 rounded-lg bg-sky-pop border-2 border-ink items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.25v13m0-13C10.8 5.5 9.2 5 7.5 5S4.2 5.5 3 6.25v13C4.2 18.5 5.8 18 7.5 18s3.3.5 4.5 1.25m0-13C13.2 5.5 14.8 5 16.5 5s3.3.5 4.5 1.25v13C19.8 18.5 18.2 18 16.5 18s-3.3.5-4.5 1.25"/></svg>
                </span>
                <h3 class="mt-3 text-sm font-extrabold text-ink">Membaca dan memahami nota</h3>
                <p class="mt-1 text-sm text-slate-600 leading-snug">Teks dan PDF dianalisis untuk mencari idea serta fakta penting.</p>
            </li>
            <li class="p-4 rounded-xl bg-white border-2 border-ink shadow-pop-sm">
                <span class="inline-flex w-9 h-9 rounded-lg bg-bubblegum border-2 border-ink items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                </span>
                <h3 class="mt-3 text-sm font-extrabold text-ink">Menyusun topik</h3>
                <p class="mt-1 text-sm text-slate-600 leading-snug">Kandungan dipecahkan kepada topik yang mudah diulang kaji.</p>
            </li>
            <li class="p-4 rounded-xl bg-white border-2 border-ink shadow-pop-sm">
                <span class="inline-flex w-9 h-9 rounded-lg bg-sun border-2 border-ink items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8.2 9a4 4 0 0 1 7.6 1.5c0 2-3 3-3 3M12 17h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0z"/></svg>
                </span>
                <h3 class="mt-3 text-sm font-extrabold text-ink">Menjana soalan dan penjelasan</h3>
                <p class="mt-1 text-sm text-slate-600 leading-snug">Kuiz, kad imbas dan penjelasan disediakan untuk setiap topik.</p>
            </li>
            <li class="p-4 rounded-xl bg-white border-2 border-ink shadow-pop-sm">
                <span class="inline-flex w-9 h-9 rounded-lg bg-mint border-2 border-ink items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 17l6-6 4 4 8-8M14 7h7v7"/></svg>
                </span>
                <h3 class="mt-3 text-sm font-extrabold text-ink">Menyesuaikan latihan</h3>
                <p class="mt-1 text-sm text-slate-600 leading-snug">Aktiviti seterusnya dipilih mengikut prestasi dan kelemahan anda.</p>
            </li>
        </ul>
    </section>

    <!-- Modes -->
    @php
        $modes = [
            ['name' => 'Penilaian Penguasaan', 'desc' => 'Soalan aras tinggi untuk topik yang perlu diperkukuh.', 'dot' => 'bg-tomato'],
            ['name' => 'Ujian Pantas', 'desc' => 'Jawab seberapa banyak soalan dalam masa 60 saat.', 'dot' => 'bg-sun'],
            ['name' => 'Sintesis Menurun', 'desc' => 'Pilih jawapan yang betul sebelum masa tamat.', 'dot' => 'bg-sky-pop'],
            ['name' => 'Padanan Konsep', 'desc' => 'Padankan istilah dengan definisi yang tepat.', 'dot' => 'bg-bubblegum'],
            ['name' => 'Menara Kefahaman', 'desc' => 'Tingkatkan kefahaman secara berperingkat.', 'dot' => 'bg-mint'],
            ['name' => 'Pengesahan Kritis', 'desc' => 'Tentukan sama ada pernyataan betul atau salah.', 'dot' => 'bg-[#bfcf95]'],
            ['name' => 'Rekonstruksi Istilah', 'desc' => 'Susun huruf untuk membentuk istilah penting.', 'dot' => 'bg-[#e5ad78]'],
            ['name' => 'Flashcard Berjarak', 'desc' => 'Ulang kaji konsep utama dengan kad imbas.', 'dot' => 'bg-[#bcaed6]'],
            ['name' => 'Penanda Aras Adaptif', 'desc' => 'Bersaing dengan bot mengikut tahap anda.', 'dot' => 'bg-[#93c3c4]'],
        ];
    @endphp

    <section class="mt-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink text-center">9 mod pembelajaran</h2>
        <p class="mt-2 text-center text-slate-600">Semua bahan dijana oleh AI daripada nota yang sama. Pilih cara yang paling sesuai dengan gaya belajar anda.</p>

        <ul class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($modes as $mode)
                <li class="flex items-start gap-3 p-4 rounded-xl bg-white border-2 border-ink">
                    <span class="mt-1 w-3.5 h-3.5 rounded-full border-2 border-ink shrink-0 {{ $mode['dot'] }}"></span>
                    <div>
                        <h3 class="text-sm font-extrabold text-ink">{{ $mode['name'] }}</h3>
                        <p class="mt-0.5 text-sm text-slate-600 leading-snug">{{ $mode['desc'] }}</p>
                    </div>
                </li>
            @endforeach
        </ul>
    </section>

    <!-- For teachers -->
    <section class="mt-16 p-6 sm:p-8 rounded-3xl bg-ink text-paper border-2 border-ink shadow-pop grid md:grid-cols-[1fr_auto] gap-6 items-center">
        <div>
            <h2 class="text-2xl font-extrabold">Untuk guru</h2>
            <p class="mt-2 text-paper/75 leading-relaxed max-w-2xl">
                Ejen AI menyediakan laporan diagnostik bagi setiap modul: ketepatan mengikut topik, soalan yang sering dijawab
                salah dan cadangan intervensi. Laporan ini boleh dicetak terus untuk perbincangan di bilik darjah.
            </p>
        </div>
        <div class="flex md:flex-col gap-2 text-sm font-bold">
            <span class="px-3 py-1.5 rounded-lg bg-sun text-ink border-2 border-paper">Ketepatan topik</span>
            <span class="px-3 py-1.5 rounded-lg bg-bubblegum text-ink border-2 border-paper">Corak kesilapan</span>
        </div>
    </section>

    <!-- CTA -->
    <section class="mt-16 text-center">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink">Sedia untuk mencuba?</h2>
        <p class="mt-2 text-slate-600">Gunakan nota anda sendiri, atau cuba dengan nota contoh terlebih dahulu.</p>
        <a href="{{ route('decks.create') }}" class="mt-6 btn-pop bg-indigo-600 text-white text-lg px-8 py-4 rounded-2xl group">
            <span>Mulakan Sekarang</span>
            <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
        </a>
    </section>
</div>
@endsection
