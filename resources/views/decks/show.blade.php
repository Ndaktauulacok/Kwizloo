@extends('layouts.app', ['title' => $deck->title, 'deck' => $deck])

@section('content')
<div class="space-y-8 py-2">
    <!-- Deck Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 border-b border-slate-200/80 pb-6">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-200">
                    Modul Rujukan
                </span>
                <span class="text-xs text-slate-400 font-mono">{{ $deck->total_words }} perkataan disahkan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                {{ $deck->title }}
            </h1>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('decks.report', $deck) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-semibold transition shadow-2xs">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <span>Laporan Guru</span>
            </a>
            <a href="{{ route('decks.activities.show', [$deck, 'cards']) }}" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-indigo-900 hover:bg-indigo-800 text-white text-xs font-semibold transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                <span>Ulang Kaji Flashcard</span>
            </a>
        </div>
    </div>

    <!-- Recommended Next Mission Hero -->
    <section class="academic-card p-6 sm:p-7 bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white shadow-md rounded-2xl relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2 max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/20 text-indigo-200 border border-indigo-400/30">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Misi Cadangan Ejen Pedagogi
                </div>
                <h2 class="text-xl sm:text-2xl font-bold tracking-tight text-white font-sans">
                    {{ $nextMission['activity_name'] }}
                </h2>
                <p class="text-sm text-indigo-100/90 leading-relaxed">
                    {{ $nextMission['rationale'] }}
                </p>
                @if($nextMission['topic_id'] > 0)
                    <div class="pt-1 text-xs text-indigo-300 font-medium">
                        Fokus Utama: <span class="text-white underline decoration-indigo-400 decoration-2 underline-offset-4">{{ $nextMission['topic_name'] }}</span>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 shrink-0">
                <a 
                    href="{{ route('decks.activities.show', [$deck, $nextMission['activity'], 'topic_id' => $nextMission['topic_id']]) }}" 
                    class="px-6 py-3 rounded-xl bg-white hover:bg-slate-100 text-slate-950 text-sm font-bold shadow-md hover:shadow-lg transition flex items-center space-x-2"
                >
                    <span>Mula Misi Ini</span>
                    <svg class="w-4 h-4 text-indigo-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Mastery Trials (Sarang Boss / Penilaian Penguasaan) -->
    @php
        $bossTopics = $topics->filter(fn($t) => $t->status === 'boss' || $t->status === 'tewas');
    @endphp

    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Penilaian Penguasaan Topik (Mastery Trials)</h2>
                <p class="text-xs text-slate-500">Kelemahan dikesan ditransformasikan menjadi cabaran pengukuhan fakta</p>
            </div>
        </div>

        @if($bossTopics->isNotEmpty())
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($bossTopics as $bt)
                    <div class="academic-card p-5 bg-white border border-slate-200 relative">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <span class="text-[11px] font-semibold uppercase tracking-wider {{ $bt->isDefeated() ? 'text-emerald-700 bg-emerald-50' : 'text-rose-700 bg-rose-50' }} px-2 py-0.5 rounded">
                                    {{ $bt->isDefeated() ? 'Dikuasai' : 'Cabaran Aktif' }}
                                </span>
                                <h3 class="text-base font-bold text-slate-900 mt-1">{{ $bt->name }}</h3>
                            </div>
                            <span class="text-lg">
                                {{ $bt->isDefeated() ? '🏆' : '⚔️' }}
                            </span>
                        </div>

                        <!-- HP Meter -->
                        <div class="space-y-1.5 mb-4">
                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span>Ketahanan Konsep:</span>
                                <span class="font-mono font-bold text-slate-700">{{ $bt->hp }} / {{ $bt->max_hp }} HP</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: {{ $bt->max_hp > 0 ? ($bt->hp / $bt->max_hp) * 100 : 0 }}%;"></div>
                            </div>
                        </div>

                        @if(!$bt->isDefeated())
                            <a 
                                href="{{ route('decks.activities.show', [$deck, 'boss', 'topic_id' => $bt->id]) }}" 
                                class="w-full block text-center px-4 py-2 rounded-xl text-xs font-semibold text-white bg-indigo-900 hover:bg-indigo-800 transition shadow-2xs"
                            >
                                Hadapi Penilaian Ini
                            </a>
                        @else
                            <div class="text-center py-2 text-xs text-emerald-700 font-semibold bg-emerald-50 rounded-xl">
                                Topik ini telah berjaya ditewaskan & dikuasai
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-6 rounded-2xl bg-white border border-slate-200 text-center text-sm text-slate-500">
                Tiada jurang kritikal dikesan buat masa ini. Semua topik berada dalam keadaan terkawal.
            </div>
        @endif
    </section>

    <!-- 9 Study Modalities -->
    <section class="space-y-3">
        <div>
            <h2 class="text-lg font-bold text-slate-900">Mod Pembelajaran Interaktif</h2>
            <p class="text-xs text-slate-500">Sembilan pendekatan pedagogi untuk mengukuhkan memori, kefahaman dan kelajuan</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($activities as $key => $act)
                <a 
                    href="{{ route('decks.activities.show', [$deck, $key]) }}" 
                    class="academic-card academic-card-hover p-5 bg-white border border-slate-200 flex flex-col justify-between group"
                >
                    <div class="space-y-2">
                        <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold text-sm group-hover:bg-indigo-900 group-hover:text-white transition">
                            {{ $loop->iteration }}
                        </div>
                        <h3 class="text-base font-bold text-slate-900 group-hover:text-indigo-900 transition">
                            {{ $act['name'] }}
                        </h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            {{ $act['description'] }}
                        </p>
                    </div>

                    <div class="pt-4 mt-2 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-indigo-700 group-hover:text-indigo-900">
                        <span>Mulakan Modul</span>
                        <svg class="w-3.5 h-3.5 group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Topic Mastery Matrix -->
    <section class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Penguasaan Topik (Mastery Matrix)</h2>
                <p class="text-xs text-slate-500">Kemajuan berdasarkan ketepatan jawapan dan rekod semakan</p>
            </div>
            <a href="{{ route('decks.report', $deck) }}" class="text-xs font-semibold text-indigo-700 hover:text-indigo-900">
                Lihat Laporan Penuh &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($topics as $t)
                @php
                    $acc = $t->accuracy_percentage;
                @endphp
                <div class="academic-card p-4 bg-white border border-slate-200 flex items-center space-x-4">
                    <div class="relative w-14 h-14 shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="{{ $acc >= 75 ? 'text-emerald-500' : ($acc >= 50 ? 'text-indigo-600' : 'text-amber-500') }}" stroke-dasharray="{{ $acc }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-xs font-bold font-mono text-slate-800">{{ $acc }}%</span>
                    </div>

                    <div class="overflow-hidden">
                        <h4 class="text-sm font-bold text-slate-900 truncate">{{ $t->name }}</h4>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $t->questions()->count() }} soalan &bull; {{ $t->attempts }} percubaan</p>
                        <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded {{ $t->isDefeated() ? 'bg-emerald-50 text-emerald-700' : ($t->isBoss() ? 'bg-rose-50 text-rose-700' : 'bg-slate-100 text-slate-600') }}">
                            {{ $t->isDefeated() ? 'Dikuasai' : ($t->isBoss() ? 'Cabaran Aktif' : 'Terkawal') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
