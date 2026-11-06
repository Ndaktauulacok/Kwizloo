@extends('layouts.app', ['title' => $deck->title, 'deck' => $deck])

@section('content')
<div class="space-y-10">
    <!-- Deck Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-5">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs font-bold">
                <span class="px-2.5 py-1 rounded-lg bg-white border-2 border-ink">{{ $deck->topics()->count() }} topik</span>
                <span class="px-2.5 py-1 rounded-lg bg-white border-2 border-ink">{{ $deck->total_words }} perkataan</span>
            </div>
            <h1 class="mt-3 text-3xl sm:text-4xl font-extrabold text-ink leading-tight max-w-3xl">
                {{ $deck->title }}
            </h1>
        </div>

        @if(!empty($nextMission['activity']))
            <a
                href="{{ route('decks.activities.show', [$deck, $nextMission['activity'], 'topic_id' => $nextMission['topic_id']]) }}"
                class="btn-pop bg-sun text-ink px-5 py-3 shrink-0 group"
                title="{{ $nextMission['rationale'] }}"
            >
                <span class="flex flex-col items-start leading-tight">
                    <span class="text-[10px] uppercase tracking-wider font-bold text-ink/60">Misi Cadangan</span>
                    <span class="text-base font-extrabold">{{ $nextMission['activity_name'] }}</span>
                </span>
                <span class="ml-2 w-8 h-8 rounded-full bg-ink text-sun flex items-center justify-center group-hover:translate-x-0.5 transition-transform">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </span>
            </a>
        @endif
    </div>

    @php
        $bossTopics = $topics->filter(fn($t) => $t->status === 'boss');
    @endphp

    @if($bossTopics->isNotEmpty())
        <section class="p-5 sm:p-6 rounded-3xl bg-ink text-paper border-2 border-ink shadow-pop">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-xl bg-tomato border-2 border-paper flex items-center justify-center animate-wiggle">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"/><line x1="13" y1="19" x2="19" y2="13"/><line x1="16" y1="16" x2="20" y2="20"/></svg>
                </span>
                <div>
                    <h2 class="text-lg font-extrabold">Topik Memerlukan Pengukuhan</h2>
                    <p class="text-sm text-paper/70">Topik berikut masih sering dijawab salah. Selesaikan penilaian penguasaan untuk mengukuhkan kefahaman anda.</p>
                </div>
            </div>

            <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($bossTopics as $bt)
                    @php $hpPercent = $bt->max_hp > 0 ? ($bt->hp / $bt->max_hp) * 100 : 0; @endphp
                    <a
                        href="{{ route('decks.activities.show', [$deck, 'boss', 'topic_id' => $bt->id]) }}"
                        class="group block p-4 rounded-2xl bg-white text-ink border-2 border-paper hover:-translate-y-0.5 transition"
                    >
                        <h3 class="font-extrabold">{{ $bt->name }}</h3>
                        <div class="mt-3 flex items-center justify-between text-xs font-bold">
                            <span class="text-tomato">Ketahanan</span>
                            <span>{{ $bt->hp }} / {{ $bt->max_hp }}</span>
                        </div>
                        <div class="mt-1 h-3 rounded-full bg-slate-100 border-2 border-ink overflow-hidden">
                            <div class="h-full bg-tomato" style="width: {{ $hpPercent }}%;"></div>
                        </div>
                        <span class="mt-3 inline-flex items-center gap-1 text-sm font-bold text-ink group-hover:gap-2 transition-all">
                            Mulakan penilaian <span aria-hidden="true">&rarr;</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @php
        $modalities = [
            'boss' => ['name' => 'Penilaian Penguasaan', 'desc' => 'Uji tahap penguasaan anda melalui soalan aras tinggi.', 'tag' => 'Aras tinggi', 'tile' => 'bg-tomato', 'meta' => '6 soalan'],
            'blitz' => ['name' => 'Ujian Pantas', 'desc' => 'Jawab seberapa banyak soalan yang boleh dalam masa 60 saat.', 'tag' => '60 saat', 'tile' => 'bg-sun', 'meta' => 'Pantas'],
            'rain' => ['name' => 'Sintesis Menurun', 'desc' => 'Pilih jawapan yang betul sebelum masa tamat.', 'tag' => 'Refleks', 'tile' => 'bg-sky-pop', 'meta' => 'Masa nyata'],
            'match' => ['name' => 'Padanan Konsep', 'desc' => 'Padankan istilah penting dengan definisi yang tepat.', 'tag' => 'Kefahaman', 'tile' => 'bg-bubblegum', 'meta' => 'Istilah & definisi'],
            'tower' => ['name' => 'Menara Kefahaman', 'desc' => 'Tingkatkan kefahaman secara berperingkat, aras demi aras.', 'tag' => '8 aras', 'tile' => 'bg-mint', 'meta' => 'Berperingkat'],
            'tf' => ['name' => 'Pengesahan Kritis', 'desc' => 'Tentukan sama ada setiap pernyataan betul atau salah.', 'tag' => 'Fakta', 'tile' => 'bg-[#b8e986]', 'meta' => '10 pernyataan'],
            'word' => ['name' => 'Rekonstruksi Istilah', 'desc' => 'Susun huruf untuk membentuk istilah penting.', 'tag' => 'Ejaan', 'tile' => 'bg-[#ffa65c]', 'meta' => 'Istilah'],
            'cards' => ['name' => 'Flashcard Berjarak', 'desc' => 'Ulang kaji konsep utama menggunakan kad imbas.', 'tag' => 'Ulang kaji', 'tile' => 'bg-[#c3b1ff]', 'meta' => 'Ingatan aktif'],
            'race' => ['name' => 'Penanda Aras Adaptif', 'desc' => 'Bersaing dengan bot yang menyesuaikan kelajuan mengikut tahap anda.', 'tag' => 'Cabaran bot', 'tile' => 'bg-[#7fe3e8]', 'meta' => 'Kelajuan'],
        ];
    @endphp

    <section>
        <div class="flex items-end justify-between gap-4 mb-5">
            <div>
                <h2 class="text-2xl font-extrabold text-ink">Pilih Mod Pembelajaran</h2>
                <p class="text-sm text-slate-600 mt-0.5">Sembilan cara berbeza untuk mengulang kaji kandungan yang sama.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach($modalities as $key => $mod)
                @php
                    $isRecommended = (!empty($nextMission['activity']) && $nextMission['activity'] === $key);
                @endphp
                <a
                    href="{{ route('decks.activities.show', [$deck, $key, 'topic_id' => $nextMission['topic_id'] ?? null]) }}"
                    class="spotlight-card overflow-hidden flex flex-col group"
                >
                    <div class="relative {{ $mod['tile'] }} border-b-2 border-ink px-5 pt-5 pb-4 flex items-start justify-between">
                        <div class="w-12 h-12 rounded-xl bg-white border-2 border-ink shadow-pop-sm flex items-center justify-center text-ink group-hover:-rotate-6 transition-transform duration-200">
                            @if($key === 'boss')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"/><line x1="13" y1="19" x2="19" y2="13"/><line x1="16" y1="16" x2="20" y2="20"/><polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"/><line x1="5" y1="14" x2="9" y2="18"/><line x1="7" y1="17" x2="4" y2="20"/></svg>
                            @elseif($key === 'blitz')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            @elseif($key === 'rain')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M16 14v6"/><path d="M8 14v6"/><path d="M12 16v6"/></svg>
                            @elseif($key === 'match')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="7" x="3" y="3" rx="1.5"/><rect width="7" height="7" x="14" y="3" rx="1.5"/><rect width="7" height="7" x="8.5" y="14" rx="1.5"/><path d="m14 8-2 6"/><path d="m7 8 3 6"/></svg>
                            @elseif($key === 'tower')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 21V10l2-2v13"/><path d="M18 21V10l-2-2v13"/><path d="M6 4h12l1 4H5l1-4z"/><path d="M10 21v-4a2 2 0 0 1 4 0v4"/><path d="M2 21h20"/><path d="M9 4V2"/><path d="M15 4V2"/></svg>
                            @elseif($key === 'tf')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"/><path d="M7 21h10"/><path d="M12 3v18"/><path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"/></svg>
                            @elseif($key === 'word')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 16 6-12 6 12"/><path d="M8 12h8"/><path d="m16 20 2 2 4-4"/></svg>
                            @elseif($key === 'cards')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="15" height="12" x="3" y="8" rx="2"/><path d="M7 4h12a2 2 0 0 1 2 2v9"/></svg>
                            @elseif($key === 'race')
                                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>
                            @endif
                        </div>

                        @if($isRecommended)
                            <span class="sticker bg-ink text-sun rotate-3">
                                <span class="w-1.5 h-1.5 rounded-full bg-sun animate-pulse"></span>
                                Cadangan ejen
                            </span>
                        @else
                            <span class="text-[11px] font-bold uppercase tracking-wider text-ink/70 bg-white/50 px-2 py-0.5 rounded-md border border-ink/20">
                                {{ $mod['tag'] }}
                            </span>
                        @endif
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-extrabold text-ink">{{ $mod['name'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600 leading-relaxed flex-1">{{ $mod['desc'] }}</p>
                        <div class="mt-4 flex items-center justify-between">
                            <span class="text-xs font-semibold text-slate-500">{{ $mod['meta'] }}</span>
                            <span class="inline-flex items-center gap-1.5 text-sm font-bold text-ink">
                                Mulakan
                                <span class="w-7 h-7 rounded-full bg-ink text-white flex items-center justify-center group-hover:translate-x-1 transition-transform">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </span>
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Topic Mastery -->
    <section>
        <div class="flex items-end justify-between gap-4 mb-5">
            <div>
                <h2 class="text-2xl font-extrabold text-ink">Penguasaan Topik</h2>
                <p class="text-sm text-slate-600 mt-0.5">{{ $topics->count() }} topik dikesan daripada nota anda.</p>
            </div>
            <a href="{{ route('decks.report', $deck) }}" class="text-sm font-bold text-indigo-700 hover:text-ink underline decoration-2 underline-offset-4 shrink-0">
                Lihat laporan guru &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($topics as $t)
                @php
                    $acc = $t->accuracy_percentage;
                    $ringColor = $acc >= 75 ? 'text-mint' : ($acc >= 50 ? 'text-indigo-600' : 'text-tomato');
                    $badgeStyle = $t->isDefeated()
                        ? 'bg-mint text-ink'
                        : ($t->isBoss() ? 'bg-tomato text-white' : 'bg-slate-100 text-slate-700');
                @endphp
                <div class="spotlight-card p-4 flex items-center gap-4">
                    <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="4" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            @if($acc > 0)<path class="{{ $ringColor }}" stroke-dasharray="{{ $acc }}, 100" stroke-width="4" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />@endif
                        </svg>
                        <span class="absolute text-xs font-extrabold text-ink">{{ $acc }}%</span>
                    </div>

                    <div class="min-w-0">
                        <h4 class="text-sm font-extrabold text-ink truncate">{{ $t->name }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $t->questions()->count() }} soalan &middot; {{ $t->attempts }} percubaan</p>
                        <span class="inline-block mt-1.5 text-[11px] font-bold px-2 py-0.5 rounded-md border-2 border-ink {{ $badgeStyle }}">
                            {{ $t->isDefeated() ? 'Dikuasai' : ($t->isBoss() ? 'Perlu pengukuhan' : 'Sedang dipelajari') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
