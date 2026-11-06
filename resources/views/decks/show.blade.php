@extends('layouts.app', ['title' => $deck->title, 'deck' => $deck])

@section('content')
<div class="space-y-6 py-2">
    <!-- Clean Editorial Deck Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 pb-2 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mb-1">
                <span>Modul Pembelajaran</span>
                <span>&bull;</span>
                <span>{{ $deck->topics()->count() }} topik</span>
                <span>&bull;</span>
                <span>{{ $deck->total_words }} perkataan sukatan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-sans">
                {{ $deck->title }}
            </h1>
        </div>

        @if(!empty($nextMission['activity']))
            <a 
                href="{{ route('decks.activities.show', [$deck, $nextMission['activity'], 'topic_id' => $nextMission['topic_id']]) }}" 
                class="inline-flex items-center gap-2.5 px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold shadow-xs transition group shrink-0"
                title="{{ $nextMission['rationale'] }}"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span class="text-slate-300">Misi Cadangan:</span>
                <span class="font-bold text-white">{{ $nextMission['activity_name'] }}</span>
                <svg class="w-3.5 h-3.5 text-slate-400 group-hover:text-white group-hover:translate-x-0.5 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
            </a>
        @endif
    </div>

    <!-- Active Mastery Trials (Only rendered when there are active trials requiring attention) -->
    @php
        $bossTopics = $topics->filter(fn($t) => $t->status === 'boss');
    @endphp

    @if($bossTopics->isNotEmpty())
        <section class="space-y-3 p-4 rounded-xl bg-rose-50/70 border border-rose-200/80">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-bold text-rose-950">Cabaran Pengukuhan Diperlukan</h2>
                    <p class="text-xs text-rose-700">Kelemahan dikesan pada topik berikut. Sila selesaikan penilaian untuk menstabilkan penguasaan.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($bossTopics as $bt)
                    <div class="p-4 rounded-lg bg-white border border-rose-200/90 shadow-2xs space-y-3">
                        <div class="flex items-start justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded">
                                    Perlu Perhatian
                                </span>
                                <h3 class="text-sm font-bold text-slate-900 mt-1.5">{{ $bt->name }}</h3>
                            </div>
                            <span class="w-6 h-6 rounded-md bg-rose-50 border border-rose-200/60 flex items-center justify-center shrink-0">
                                <svg class="w-3 h-3 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
                                    <line x1="13" y1="19" x2="19" y2="13"></line>
                                    <line x1="16" y1="16" x2="20" y2="20"></line>
                                </svg>
                            </span>
                        </div>

                        <div class="space-y-1">
                            <div class="flex items-center justify-between text-[11px] text-slate-500">
                                <span>Ketahanan:</span>
                                <span class="font-mono font-medium text-slate-700">{{ $bt->hp }} / {{ $bt->max_hp }} HP</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-rose-500 h-1.5 rounded-full" style="width: {{ $bt->max_hp > 0 ? ($bt->hp / $bt->max_hp) * 100 : 0 }}%;"></div>
                            </div>
                        </div>

                        <a 
                            href="{{ route('decks.activities.show', [$deck, 'boss', 'topic_id' => $bt->id]) }}" 
                            class="w-full block text-center px-3 py-1.5 rounded-md text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition"
                        >
                            Hadapi Penilaian Ini &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- 9 Study Modalities: Refined Professional Glass Folder Cards -->
    @php
        $modalities = [
            'boss' => [
                'name' => 'Penilaian Penguasaan',
                'eng' => 'Mastery Assessment',
                'desc' => 'Tewaskan jurang pemahaman dengan menjawab soalan aras tinggi.',
                'badgeText' => 'Aras Tinggi',
                'iconBg' => 'bg-rose-50 text-rose-700 border border-rose-200/60',
                'meta' => '6 Soalan Sumber',
            ],
            'blitz' => [
                'name' => 'Ujian Pantas',
                'eng' => 'Speed Recall',
                'desc' => '60 saat untuk menguji kelancaran pengingatan konsep.',
                'badgeText' => '60 Saat',
                'iconBg' => 'bg-amber-50 text-amber-700 border border-amber-200/60',
                'meta' => 'Pengecaman Pantas',
            ],
            'rain' => [
                'name' => 'Sintesis Menurun',
                'eng' => 'Descending Synthesis',
                'desc' => 'Tangkap jawapan tepat sebelum masa berlalu.',
                'badgeText' => 'Refleks',
                'iconBg' => 'bg-sky-50 text-sky-700 border border-sky-200/60',
                'meta' => 'Masa Nyata',
            ],
            'match' => [
                'name' => 'Padanan Konsep',
                'eng' => 'Term Association',
                'desc' => 'Padankan istilah penting dengan definisinya secara tepat.',
                'badgeText' => 'Kefahaman',
                'iconBg' => 'bg-purple-50 text-purple-700 border border-purple-200/60',
                'meta' => 'Definisi & Hubungan',
            ],
            'tower' => [
                'name' => 'Menara Kefahaman',
                'eng' => 'Ascending Comprehension',
                'desc' => 'Bina tangga ilmu langkah demi langkah mengikut tahap.',
                'badgeText' => 'Tingkat Ilmu',
                'iconBg' => 'bg-emerald-50 text-emerald-700 border border-emerald-200/60',
                'meta' => '8 Aras Sukatan',
            ],
            'tf' => [
                'name' => 'Pengesahan Kritis',
                'eng' => 'Critical Verification',
                'desc' => 'Analisis kebenaran pernyataan fakta dan konsep sukatan.',
                'badgeText' => 'Fakta / Palsu',
                'iconBg' => 'bg-teal-50 text-teal-700 border border-teal-200/60',
                'meta' => 'Kajian Pernyataan',
            ],
            'word' => [
                'name' => 'Rekonstruksi Istilah',
                'eng' => 'Term Reconstruction',
                'desc' => 'Eja dan susun huruf untuk kenal pasti istilah saintifik utama.',
                'badgeText' => 'Ejaan Saintifik',
                'iconBg' => 'bg-orange-50 text-orange-700 border border-orange-200/60',
                'meta' => 'Istilah Verbatim',
            ],
            'cards' => [
                'name' => 'Flashcard Berjarak',
                'eng' => 'Spaced Recall',
                'desc' => 'Ulang kaji aktif konsep asas dengan kad imbasan digital.',
                'badgeText' => 'Ulang Kaji Asas',
                'iconBg' => 'bg-indigo-50 text-indigo-700 border border-indigo-200/60',
                'meta' => 'Pengingatan Aktif',
            ],
            'race' => [
                'name' => 'Penanda Aras Adaptif',
                'eng' => 'Pacing Benchmark',
                'desc' => 'Berlumba dengan bot pembelajaran beraras automatik.',
                'badgeText' => 'Cabaran Bot',
                'iconBg' => 'bg-cyan-50 text-cyan-700 border border-cyan-200/60',
                'meta' => 'Kadar Kepantasan',
            ],
        ];
    @endphp

    <section class="space-y-3.5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider font-mono">
                Mod Pembelajaran (9)
            </h2>
            <span class="text-xs text-slate-400">
                Pilih mana-mana mod atau ikuti cadangan ejen
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($modalities as $key => $mod)
                @php
                    $isRecommended = (!empty($nextMission['activity']) && $nextMission['activity'] === $key);
                @endphp
                <a 
                    href="{{ route('decks.activities.show', [$deck, $key, 'topic_id' => $nextMission['topic_id'] ?? null]) }}" 
                    x-data="{
                        handleMove(e) {
                            const r = $el.getBoundingClientRect();
                            $el.style.setProperty('--mouse-x', (e.clientX - r.left) + 'px');
                            $el.style.setProperty('--mouse-y', (e.clientY - r.top) + 'px');
                        }
                    }"
                    @mousemove="handleMove($event)"
                    style="--spotlight-glow: rgba(99, 102, 241, 0.06); --spotlight-border-glow: rgba(99, 102, 241, 0.28);"
                    class="spotlight-card p-5 flex flex-col justify-between group cursor-pointer relative {{ $isRecommended ? 'ring-1.5 ring-indigo-500/50 shadow-sm' : '' }}"
                >
                    <!-- Spotlight Border & Cursor Glow (21st.dev / Spotlight Card) -->
                    <div class="spotlight-border"></div>
                    <div class="spotlight-glow"></div>

                    <div class="space-y-3.5 relative z-10">
                        <!-- Glass Folder Tab Header (21st.dev / Glass Folder Cards) -->
                        <div class="flex items-center justify-between">
                            <!-- Left: Frosted Tab Badge or Recommended Pill -->
                            @if($isRecommended)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700 border border-indigo-200/80 shadow-2xs">
                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-600 animate-pulse"></span>
                                    <span class="text-[10px] font-bold uppercase tracking-wider">
                                        Cadangan Ejen
                                    </span>
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md glass-folder-tab border border-slate-200/70">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                    <span class="text-[10px] font-mono font-medium text-slate-600 uppercase tracking-wider">
                                        {{ $mod['badgeText'] }}
                                    </span>
                                </div>
                            @endif

                            <!-- Right: Folder Index Notch -->
                            <div class="inline-flex items-center gap-1 text-[11px] font-mono font-medium text-slate-400 bg-white/70 px-2 py-0.5 rounded border border-slate-200/50">
                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                <span>#0{{ $loop->iteration }}</span>
                            </div>
                        </div>

                        <!-- Card Icon & Title Row -->
                        <div class="flex items-start gap-3 pt-0.5">
                            <div class="w-10 h-10 rounded-xl {{ $mod['iconBg'] }} flex items-center justify-center shrink-0 transition-transform duration-200 group-hover:scale-105">
                                @if($key === 'boss')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="14.5 17.5 3 6 3 3 6 3 17.5 14.5"></polyline>
                                        <line x1="13" y1="19" x2="19" y2="13"></line>
                                        <line x1="16" y1="16" x2="20" y2="20"></line>
                                        <polyline points="14.5 6.5 18 3 21 3 21 6 17.5 9.5"></polyline>
                                        <line x1="5" y1="14" x2="9" y2="18"></line>
                                        <line x1="7" y1="17" x2="4" y2="20"></line>
                                    </svg>
                                @elseif($key === 'blitz')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                                    </svg>
                                @elseif($key === 'rain')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
                                        <path d="M16 14v6"></path>
                                        <path d="M8 14v6"></path>
                                        <path d="M12 16v6"></path>
                                    </svg>
                                @elseif($key === 'match')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="7" height="7" x="3" y="3" rx="1.5"></rect>
                                        <rect width="7" height="7" x="14" y="3" rx="1.5"></rect>
                                        <rect width="7" height="7" x="8.5" y="14" rx="1.5"></rect>
                                        <path d="m14 8-2 6"></path>
                                        <path d="m7 8 3 6"></path>
                                    </svg>
                                @elseif($key === 'tower')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M6 21V10l2-2v13"></path>
                                        <path d="M18 21V10l-2-2v13"></path>
                                        <path d="M6 4h12l1 4H5l1-4z"></path>
                                        <path d="M10 21v-4a2 2 0 0 1 4 0v4"></path>
                                        <path d="M2 21h20"></path>
                                        <path d="M9 4V2"></path>
                                        <path d="M15 4V2"></path>
                                    </svg>
                                @elseif($key === 'tf')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m16 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"></path>
                                        <path d="m2 16 3-8 3 8c-.87.65-1.92 1-3 1s-2.13-.35-3-1Z"></path>
                                        <path d="M7 21h10"></path>
                                        <path d="M12 3v18"></path>
                                        <path d="M3 7h2c2 0 5-1 7-2 2 1 5 2 7 2h2"></path>
                                    </svg>
                                @elseif($key === 'word')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="m6 16 6-12 6 12"></path>
                                        <path d="M8 12h8"></path>
                                        <path d="m16 20 2 2 4-4"></path>
                                    </svg>
                                @elseif($key === 'cards')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="15" height="12" x="3" y="8" rx="2"></rect>
                                        <path d="M7 4h12a2 2 0 0 1 2 2v9"></path>
                                    </svg>
                                @elseif($key === 'race')
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"></path>
                                        <line x1="4" y1="22" x2="4" y2="15"></line>
                                    </svg>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-950 transition-colors">
                                    {{ $mod['name'] }}
                                </h3>
                                <span class="text-[11px] font-medium text-slate-400 block truncate">
                                    {{ $mod['eng'] }}
                                </span>
                            </div>
                        </div>

                        <!-- Description -->
                        <p class="text-xs text-slate-500 leading-relaxed font-normal">
                            {{ $mod['desc'] }}
                        </p>
                    </div>

                    <!-- Glass Folder Footer -->
                    <div class="mt-4 pt-3 border-t border-slate-200/70 flex items-center justify-between relative z-10">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md font-semibold text-xs text-slate-700 bg-white border border-slate-200 shadow-2xs group-hover:border-indigo-300 group-hover:text-indigo-600 transition-colors">
                            <span>Buka Modul</span>
                            <svg class="w-3.5 h-3.5 group-hover:translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </span>
                        <span class="text-[11px] font-mono text-slate-400 font-medium">
                            {{ $mod['meta'] }}
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Topic Mastery Matrix with Subtle Frosted Glass -->
    <section class="space-y-3 pt-2">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-800 uppercase tracking-wider font-mono">
                Penguasaan Topik ({{ $topics->count() }})
            </h2>
            <a href="{{ route('decks.report', $deck) }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                <span>Lihat Laporan Guru Penuh</span>
                <span>&rarr;</span>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            @foreach($topics as $t)
                @php
                    $acc = $t->accuracy_percentage;
                    $ringColor = $acc >= 75 ? 'text-emerald-500' : ($acc >= 50 ? 'text-indigo-600' : 'text-amber-500');
                    $badgeStyle = $t->isDefeated() 
                        ? 'bg-emerald-50 text-emerald-700 border-emerald-200' 
                        : ($t->isBoss() ? 'bg-rose-50 text-rose-700 border-rose-200' : 'bg-slate-100 text-slate-600 border-slate-200');
                @endphp
                <div class="spotlight-card p-4 flex items-center space-x-3.5 hover:shadow-xs transition">
                    <div class="relative w-11 h-11 shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="{{ $ringColor }}" stroke-dasharray="{{ $acc }}, 100" stroke-width="3.5" stroke-linecap="round" stroke-currentColor fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <span class="absolute text-[11px] font-bold font-mono text-slate-800">{{ $acc }}%</span>
                    </div>

                    <div class="overflow-hidden">
                        <h4 class="text-xs font-bold text-slate-900 truncate">{{ $t->name }}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">{{ $t->questions()->count() }} soalan &bull; {{ $t->attempts }} percubaan</p>
                        <span class="inline-block mt-1 text-[10px] font-semibold px-2 py-0.5 rounded border {{ $badgeStyle }}">
                            {{ $t->isDefeated() ? 'Dikuasai' : ($t->isBoss() ? 'Perlu Pengukuhan' : 'Terkawal') }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
</div>
@endsection
