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
            Tidak perlu daftar akaun
        </span>
        <h1 class="mt-5 text-3xl sm:text-5xl font-extrabold text-ink leading-tight">
            Cara KwizLoo <span class="marker-underline">berfungsi</span>
        </h1>
        <p class="mt-4 text-base sm:text-lg text-slate-600 max-w-2xl mx-auto leading-relaxed">
            Daripada nota biasa kepada sesi ulang kaji yang interaktif, dalam tiga langkah mudah.
        </p>
    </header>

    <!-- Steps -->
    <ol class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-5">
        <li class="relative p-6 rounded-2xl bg-sky-pop/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-sky-pop border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">1</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">Masukkan nota</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                Tampal teks atau muat naik dokumen PDF. KwizLoo akan mengenal pasti topik-topik utama dalam nota anda secara automatik.
            </p>
        </li>
        <li class="relative p-6 rounded-2xl bg-sun/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-sun border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">2</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">Pilih mod pembelajaran</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                Setiap soalan, kad imbas dan aktiviti dijana daripada nota yang sama, dan setiap jawapan disertakan petikan sumbernya.
            </p>
        </li>
        <li class="relative p-6 rounded-2xl bg-mint/30 border-2 border-ink">
            <div class="w-12 h-12 rounded-full bg-mint border-2 border-ink shadow-pop-sm flex items-center justify-center font-display font-extrabold text-xl">3</div>
            <h2 class="mt-5 text-xl font-extrabold text-ink">Pantau kemajuan</h2>
            <p class="mt-2 text-sm text-slate-700 leading-relaxed">
                Lihat tahap penguasaan setiap topik. Topik yang sering dijawab salah akan dijadikan cabaran pengukuhan secara automatik.
            </p>
        </li>
    </ol>

    <!-- Modes -->
    @php
        $modes = [
            ['name' => 'Penilaian Penguasaan', 'desc' => 'Soalan aras tinggi untuk topik yang perlu diperkukuh.', 'dot' => 'bg-tomato'],
            ['name' => 'Ujian Pantas', 'desc' => 'Jawab seberapa banyak soalan dalam masa 60 saat.', 'dot' => 'bg-sun'],
            ['name' => 'Sintesis Menurun', 'desc' => 'Pilih jawapan yang betul sebelum masa tamat.', 'dot' => 'bg-sky-pop'],
            ['name' => 'Padanan Konsep', 'desc' => 'Padankan istilah dengan definisi yang tepat.', 'dot' => 'bg-bubblegum'],
            ['name' => 'Menara Kefahaman', 'desc' => 'Tingkatkan kefahaman secara berperingkat.', 'dot' => 'bg-mint'],
            ['name' => 'Pengesahan Kritis', 'desc' => 'Tentukan sama ada pernyataan betul atau salah.', 'dot' => 'bg-[#b8e986]'],
            ['name' => 'Rekonstruksi Istilah', 'desc' => 'Susun huruf untuk membentuk istilah penting.', 'dot' => 'bg-[#ffa65c]'],
            ['name' => 'Flashcard Berjarak', 'desc' => 'Ulang kaji konsep utama dengan kad imbas.', 'dot' => 'bg-[#c3b1ff]'],
            ['name' => 'Penanda Aras Adaptif', 'desc' => 'Bersaing dengan bot mengikut tahap anda.', 'dot' => 'bg-[#7fe3e8]'],
        ];
    @endphp

    <section class="mt-16">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink text-center">9 mod pembelajaran</h2>
        <p class="mt-2 text-center text-slate-600">Pilih cara yang paling sesuai dengan gaya belajar anda.</p>

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
                Setiap modul disertakan laporan diagnostik: ketepatan mengikut topik, soalan yang sering dijawab salah dan cadangan
                intervensi. Laporan ini boleh dicetak terus untuk perbincangan di bilik darjah.
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
