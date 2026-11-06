@extends('layouts.app', ['title' => 'Selamat Datang'])

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Hero -->
    <section class="grid lg:grid-cols-[1fr_1.05fr] gap-8 lg:gap-4 items-center pt-2 lg:pt-6 pb-12">
        <div class="order-2 lg:order-1 text-center lg:text-left">
            <span class="sticker bg-white text-ink -rotate-2">
                <span class="w-2 h-2 rounded-full bg-tomato"></span>
                Selamat datang ke KwizLoo
            </span>

            <h1 class="mt-5 text-4xl sm:text-5xl lg:text-[3.5rem] font-extrabold text-ink leading-[1.05]">
                Belajar dengan cara yang <span class="marker-underline">lebih menyeronokkan</span>.
            </h1>

            <p class="mt-5 text-base sm:text-lg text-slate-600 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                KwizLoo mengubah nota, sukatan pelajaran atau dokumen PDF anda menjadi kuiz, kad imbas dan
                aktiviti interaktif. Sesuai untuk murid, pelajar dan guru.
            </p>

            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-lg px-8 py-4 rounded-2xl group">
                    <span>Mulakan Sekarang</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14m-6-6 6 6-6 6"/></svg>
                </a>
                <a href="#cara" class="text-sm font-bold text-slate-700 hover:text-ink underline decoration-2 decoration-sun underline-offset-4">
                    Lihat cara ia berfungsi
                </a>
            </div>

            <dl class="mt-10 grid grid-cols-3 gap-3 max-w-md mx-auto lg:mx-0">
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center">
                    <dt class="sr-only">Mod pembelajaran</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">9</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">mod pembelajaran</dd>
                </div>
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center">
                    <dt class="sr-only">Format disokong</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">PDF</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">atau teks biasa</dd>
                </div>
                <div class="p-3 rounded-xl bg-white border-2 border-ink text-center">
                    <dt class="sr-only">Laporan</dt>
                    <dd class="font-display text-2xl font-extrabold text-ink">Guru</dd>
                    <dd class="text-[11px] font-semibold text-slate-600">laporan prestasi</dd>
                </div>
            </dl>
        </div>

        <!-- Mascot holding floating study cards -->
        <div class="order-1 lg:order-2 mascot w-full max-w-[560px] mx-auto">
            <svg viewBox="0 0 560 540" class="w-full h-auto overflow-visible" role="img" aria-labelledby="mascot-title">
                <title id="mascot-title">Maskot KwizLoo menghulurkan tangan sambil memegang kad pembelajaran yang terapung</title>

                <!-- Sparkles -->
                <g fill="#ffc83d" stroke="#1c1915" stroke-width="2.5" stroke-linejoin="round">
                    <path class="mascot-spark" d="M62 80 Q62 92 74 92 Q62 92 62 104 Q62 92 50 92 Q62 92 62 80Z"/>
                    <path class="mascot-spark" style="animation-delay:-0.8s" d="M520 40 Q520 52 532 52 Q520 52 520 64 Q520 52 508 52 Q520 52 520 40Z"/>
                    <path class="mascot-spark" style="animation-delay:-1.6s" d="M512 318 Q512 328 522 328 Q512 328 512 338 Q512 328 502 328 Q512 328 512 318Z"/>
                    <path class="mascot-spark" style="animation-delay:-1.2s" d="M40 340 Q40 350 50 350 Q40 350 40 360 Q40 350 30 350 Q40 350 40 340Z"/>
                    <path class="mascot-spark" style="animation-delay:-2s" d="M196 120 Q196 128 204 128 Q196 128 196 136 Q196 128 188 128 Q196 128 196 120Z"/>
                </g>

                <!-- Ground shadow -->
                <ellipse cx="280" cy="510" rx="112" ry="13" fill="#1c1915" opacity="0.12"/>

                <!-- Character -->
                <g class="mascot-bob">
                    <!-- Feet -->
                    <ellipse cx="240" cy="490" rx="32" ry="15" fill="#ffc83d" stroke="#1c1915" stroke-width="4"/>
                    <ellipse cx="320" cy="490" rx="32" ry="15" fill="#ffc83d" stroke="#1c1915" stroke-width="4"/>

                    <!-- Arms (behind body) -->
                    <path d="M205 362 C150 362 118 332 106 290" fill="none" stroke="#1c1915" stroke-width="27" stroke-linecap="round"/>
                    <path d="M205 362 C150 362 118 332 106 290" fill="none" stroke="#ffc83d" stroke-width="17" stroke-linecap="round"/>
                    <path d="M355 362 C410 362 442 332 454 290" fill="none" stroke="#1c1915" stroke-width="27" stroke-linecap="round"/>
                    <path d="M355 362 C410 362 442 332 454 290" fill="none" stroke="#ffc83d" stroke-width="17" stroke-linecap="round"/>

                    <!-- Open palms, facing up -->
                    <path d="M76 272 Q80 300 106 302 Q132 300 136 270 Q122 279 106 279 Q90 279 76 272Z" fill="#ffc83d" stroke="#1c1915" stroke-width="4" stroke-linejoin="round"/>
                    <path d="M484 272 Q480 300 454 302 Q428 300 424 270 Q438 279 454 279 Q470 279 484 272Z" fill="#ffc83d" stroke="#1c1915" stroke-width="4" stroke-linejoin="round"/>

                    <!-- Magic lines rising from palms -->
                    <g stroke="#1c1915" stroke-width="3" stroke-linecap="round" opacity="0.45">
                        <path d="M90 256 L84 244"/><path d="M106 252 L106 238"/><path d="M122 256 L128 244"/>
                        <path d="M438 256 L432 244"/><path d="M454 252 L454 238"/><path d="M470 256 L476 244"/>
                    </g>

                    <!-- Antenna -->
                    <path d="M282 252 Q276 218 292 198" fill="none" stroke="#1c1915" stroke-width="5" stroke-linecap="round"/>
                    <circle cx="294" cy="190" r="13" fill="#ff6b4a" stroke="#1c1915" stroke-width="4"/>
                    <circle cx="290" cy="186" r="3.5" fill="#ffffff" opacity="0.8"/>

                    <!-- Body -->
                    <rect x="188" y="256" width="200" height="236" rx="100" fill="#1c1915"/>
                    <rect x="180" y="248" width="200" height="236" rx="100" fill="#ffc83d" stroke="#1c1915" stroke-width="5"/>
                    <ellipse cx="280" cy="420" rx="62" ry="46" fill="#ffe08a"/>

                    <!-- Eyes -->
                    <g class="mascot-eye">
                        <ellipse cx="250" cy="322" rx="17" ry="20" fill="#ffffff" stroke="#1c1915" stroke-width="4"/>
                        <circle cx="253" cy="326" r="9" fill="#1c1915"/>
                        <circle cx="256" cy="321" r="3" fill="#ffffff"/>
                    </g>
                    <g class="mascot-eye">
                        <ellipse cx="310" cy="322" rx="17" ry="20" fill="#ffffff" stroke="#1c1915" stroke-width="4"/>
                        <circle cx="313" cy="326" r="9" fill="#1c1915"/>
                        <circle cx="316" cy="321" r="3" fill="#ffffff"/>
                    </g>

                    <!-- Cheeks & smile -->
                    <ellipse cx="224" cy="358" rx="13" ry="8" fill="#ff8fc7" opacity="0.85"/>
                    <ellipse cx="336" cy="358" rx="13" ry="8" fill="#ff8fc7" opacity="0.85"/>
                    <path d="M260 356 Q280 386 300 356 Z" fill="#1c1915" stroke="#1c1915" stroke-width="4" stroke-linejoin="round"/>
                    <ellipse cx="280" cy="368" rx="8" ry="4" fill="#ff6b4a"/>
                </g>

                <!-- Floating card above left hand: speed quiz -->
                <g class="mascot-hover">
                    <g transform="rotate(-8)">
                        <rect x="27" y="123" width="176" height="96" rx="16" fill="#1c1915"/>
                        <rect x="22" y="118" width="176" height="96" rx="16" fill="#5aa9ff" stroke="#1c1915" stroke-width="4"/>
                        <text x="40" y="146" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="12" font-weight="700" letter-spacing="1" fill="#1c1915" opacity="0.7">UJIAN PANTAS</text>
                        <text x="40" y="182" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="30" font-weight="800" fill="#1c1915">00:42</text>
                        <rect x="40" y="192" width="140" height="12" rx="6" fill="#ffffff" fill-opacity="0.6" stroke="#1c1915" stroke-width="3"/>
                        <rect x="40" y="192" width="92" height="12" rx="6" fill="#1c1915"/>
                    </g>
                </g>

                <!-- Floating card above right hand: true or false -->
                <g class="mascot-hover" style="animation-delay:-2.1s">
                    <g transform="rotate(7)">
                        <rect x="367" y="113" width="180" height="104" rx="16" fill="#1c1915"/>
                        <rect x="362" y="108" width="180" height="104" rx="16" fill="#ff8fc7" stroke="#1c1915" stroke-width="4"/>
                        <text x="378" y="135" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="11.5" font-weight="700" letter-spacing="1" fill="#1c1915" opacity="0.7">BETUL ATAU SALAH?</text>
                        <text x="378" y="158" font-family="Newsreader, Georgia, serif" font-size="15" font-style="italic" fill="#1c1915">“Klorofil menyerap</text>
                        <text x="378" y="176" font-family="Newsreader, Georgia, serif" font-size="15" font-style="italic" fill="#1c1915">cahaya hijau.”</text>
                        <rect x="378" y="184" width="70" height="20" rx="6" fill="#ffffff" stroke="#1c1915" stroke-width="3"/>
                        <text x="413" y="198" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="11" font-weight="700" fill="#1c1915">Salah</text>
                        <rect x="456" y="184" width="70" height="20" rx="6" fill="#1c1915" stroke="#1c1915" stroke-width="3"/>
                        <text x="491" y="198" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="11" font-weight="700" fill="#ffffff">Betul</text>
                    </g>
                </g>

                <!-- Floating badge above head -->
                <g class="mascot-hover" style="animation-delay:-1s">
                    <g transform="rotate(10)">
                        <circle cx="344" cy="92" r="30" fill="#1c1915"/>
                        <circle cx="340" cy="88" r="30" fill="#2ec4a0" stroke="#1c1915" stroke-width="4"/>
                        <text x="340" y="97" text-anchor="middle" font-family="Bricolage Grotesque, Instrument Sans, sans-serif" font-size="24" font-weight="800" fill="#1c1915">A+</text>
                    </g>
                </g>
            </svg>
        </div>
    </section>

    <!-- How it works -->
    <section id="cara" class="scroll-mt-24 pb-6">
        <h2 class="text-2xl sm:text-3xl font-extrabold text-ink text-center">Tiga langkah mudah</h2>
        <p class="mt-2 text-center text-slate-600">Tidak perlu daftar akaun. Terus mula belajar.</p>

        <ol class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-5">
            <li class="p-5 rounded-2xl bg-sky-pop/30 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-sky-pop border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">1</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Masukkan nota</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">Tampal teks atau muat naik dokumen PDF. KwizLoo akan mengenal pasti topik-topik utama secara automatik.</p>
            </li>
            <li class="p-5 rounded-2xl bg-sun/30 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-sun border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">2</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Pilih mod pembelajaran</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">Kuiz pantas, kad imbas, padanan konsep dan enam lagi mod — semuanya dijana daripada nota yang sama.</p>
            </li>
            <li class="p-5 rounded-2xl bg-mint/30 border-2 border-ink">
                <div class="w-10 h-10 rounded-full bg-mint border-2 border-ink flex items-center justify-center font-display font-extrabold text-lg">3</div>
                <h3 class="mt-4 text-lg font-extrabold text-ink">Pantau kemajuan</h3>
                <p class="mt-1.5 text-sm text-slate-700 leading-relaxed">Lihat penguasaan setiap topik dan dapatkan cadangan aktiviti seterusnya. Guru boleh mencetak laporan penuh.</p>
            </li>
        </ol>

        <div class="mt-10 text-center">
            <a href="{{ route('decks.create') }}" class="btn-pop bg-sun text-ink text-base px-7 py-3">
                Mulakan Sekarang
            </a>
        </div>
    </section>
</div>
@endsection
