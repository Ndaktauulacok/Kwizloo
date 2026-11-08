@extends('layouts.app', ['title' => 'Laporan Diagnostik Guru: ' . $deck->title, 'deck' => $deck])

@section('content')
<div class="max-w-5xl mx-auto py-2 space-y-8">
    <!-- Header with Print / Export Controls -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b-2 border-dashed border-slate-300 pb-6 print:border-none">
        <div>
            <div class="flex items-center gap-2 mb-1.5">
                <span class="sticker bg-sun text-ink -rotate-1">
                    Audit Pedagogi & Diagnostik
                </span>
                <span class="text-xs text-slate-400 font-mono">Dijana: {{ now()->format('d M Y, H:i') }}</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-ink">
                Laporan Diagnostik Pembelajaran
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Modul: <span class="font-semibold text-slate-800">{{ $deck->title }}</span> ({{ $deck->total_words }} perkataan teks sumber)
            </p>
        </div>

        <div class="flex items-center gap-2.5 print:hidden">
            <button 
                type="button" 
                onclick="window.print()" 
                class="btn-pop bg-white text-ink px-4 py-2 text-xs"
            >
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                <span>Cetak Laporan</span>
            </button>
            <a 
                href="{{ route('decks.show', $deck) }}" 
                class="btn-pop bg-indigo-600 text-white px-4 py-2 text-xs"
            >
                Kembali ke Hab
            </a>
        </div>
    </div>

    <!-- Aggregate Performance Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-5 rounded-2xl border-2 border-ink shadow-pop bg-sky-pop/45">
            <span class="text-[11px] uppercase font-bold text-ink/70 block tracking-wider">Jumlah Sesi Latihan</span>
            <div class="text-4xl font-extrabold font-display text-slate-900 mt-1">{{ $totalSessions }}</div>
            <span class="text-xs text-ink/70 mt-0.5 block">Sesi interaktif direkodkan</span>
        </div>

        <div class="p-5 rounded-2xl border-2 border-ink shadow-pop bg-mint/40">
            <span class="text-[11px] uppercase font-bold text-ink/70 block tracking-wider">Kadar Penguasaan Purata</span>
            <div class="text-4xl font-extrabold font-display {{ $overallAccuracy >= 75 ? 'text-emerald-600' : ($overallAccuracy >= 50 ? 'text-indigo-600' : 'text-amber-600') }} mt-1">
                {{ $overallAccuracy }}%
            </div>
            <span class="text-xs text-ink/70 mt-0.5 block">Daripada keseluruhan soalan</span>
        </div>

        <div class="p-5 rounded-2xl border-2 border-ink shadow-pop bg-sun/50">
            <span class="text-[11px] uppercase font-bold text-ink/70 block tracking-wider">Status Jurang Penguasaan</span>
            <div class="text-4xl font-extrabold font-display text-slate-900 mt-1">
                {{ $topics->filter(fn($t) => $t->status === 'boss')->count() }}
            </div>
            <span class="text-xs text-ink/70 mt-0.5 block">Topik memerlukan fokus intensif</span>
        </div>

        <div class="p-5 rounded-2xl border-2 border-ink shadow-pop bg-[#c3b1ff]/55">
            <span class="text-[11px] uppercase font-bold text-ink/70 block tracking-wider">Intervensi Ejen AI</span>
            <div class="text-4xl font-extrabold font-display text-indigo-700 mt-1">
                {{ $logs->count() }}
            </div>
            <span class="text-xs text-ink/70 mt-0.5 block">Pelarasan pedagogi automatik</span>
        </div>
    </div>

    <!-- AI Teacher Insights Section -->
    <div 
        x-data="teacherAiReport({
            generateUrl: '{{ route('decks.report.generate', $deck) }}',
            statusUrl: '{{ route('decks.report.status', $deck) }}',
            initialStatus: '{{ $aiReport ? $aiReport->status : 'none' }}',
            initialSummary: {{ Js::from($aiReport?->summary ?? '') }},
            initialMisconceptions: {{ Js::from($aiReport?->misconceptions ?? []) }},
            initialRecommendations: {{ Js::from($aiReport?->recommendations ?? []) }},
            hasSessions: {{ $totalSessions > 0 ? 'true' : 'false' }}
        })"
        class="card bg-white border-3 border-ink shadow-pop rounded-3xl p-6 sm:p-8 relative overflow-hidden"
    >
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-5 border-b-2 border-dashed border-slate-200">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="sticker bg-bubblegum text-ink -rotate-1 text-xs">
                        Dikuasakan oleh Gemini AI
                    </span>
                    <span 
                        class="text-xs font-bold px-2.5 py-0.5 rounded-full border border-ink"
                        :class="{
                            'bg-mint text-ink': status === 'completed',
                            'bg-sun text-ink animate-pulse': status === 'processing' || status === 'pending',
                            'bg-slate-100 text-slate-600': status === 'none',
                            'bg-tomato/20 text-tomato': status === 'failed'
                        }"
                        x-text="statusBadgeText"
                    ></span>
                </div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-ink">
                    Analisis Diagnostik Pintar (AI Teacher Insights)
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    Sintesis corak pemikiran murid, analisis jurang konsepsi, dan panduan intervensi pengajaran
                </p>
            </div>

            <div class="shrink-0 print:hidden">
                <template x-if="status !== 'completed' && status !== 'processing' && status !== 'pending'">
                    <button
                        type="button"
                        @click="generateReport()"
                        :disabled="!hasSessions || loading"
                        class="btn-pop bg-sun text-ink font-bold px-4 py-2 text-xs rounded-xl flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Jana Analisis AI</span>
                    </button>
                </template>

                <template x-if="status === 'completed'">
                    <button
                        type="button"
                        @click="generateReport()"
                        class="btn-pop bg-white text-ink font-bold px-3 py-1.5 text-xs rounded-xl flex items-center gap-1.5"
                        title="Kemas kini analisis semula"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span>Kemas Kini AI</span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Processing State -->
        <template x-if="status === 'processing' || status === 'pending'">
            <div class="py-10 text-center space-y-3">
                <div class="inline-flex p-3 rounded-2xl bg-sun/30 border-2 border-ink animate-bounce">
                    <svg class="animate-spin h-6 w-6 text-ink" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                </div>
                <h3 class="font-extrabold text-base text-ink">AI sedang menganalisis data pembelajaran...</h3>
                <p class="text-xs text-slate-500 max-w-md mx-auto">
                    Mengenal pasti corak kesilapan dan merangka cadangan tindakan pemulihan. Status disemak secara automatik.
                </p>
            </div>
        </template>

        <!-- No sessions warning -->
        <template x-if="status === 'none' && !hasSessions">
            <div class="py-6 px-4 my-4 rounded-2xl bg-amber-50 border-2 border-dashed border-amber-300 text-center">
                <p class="text-xs font-bold text-amber-800">
                    Laporan diagnostik AI memerlukan sekurang-kurangnya satu sesi aktiviti murid untuk menganalisis jurang pembelajaran.
                </p>
                <p class="text-[11px] text-amber-700 mt-1">
                    Minta pelajar melengkapkan ujian pantas atau kad imbasan terlebih dahulu.
                </p>
            </div>
        </template>

        <!-- Completed State -->
        <template x-if="status === 'completed'">
            <div class="pt-6 space-y-6">
                <!-- Executive Summary -->
                <div class="p-5 rounded-2xl bg-sky-pop/25 border-2 border-ink">
                    <span class="text-[11px] uppercase font-black tracking-wider text-ink/70 block mb-1">Rumusan Eksekutif Pembelajaran:</span>
                    <p class="text-sm text-ink leading-relaxed font-medium" x-text="summary"></p>
                </div>

                <!-- 2-Column Breakdown: Misconceptions vs Recommendations -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <!-- Misconceptions -->
                    <div class="p-5 rounded-2xl bg-tomato/10 border-2 border-ink">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-5 h-5 rounded-md bg-tomato text-white flex items-center justify-center font-bold text-xs border border-ink">!</span>
                            <h4 class="font-display font-extrabold text-sm text-ink uppercase tracking-wide">Salah Faham Konsep Dikesan</h4>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-800">
                            <template x-for="(item, idx) in misconceptions" :key="idx">
                                <li class="flex items-start gap-2">
                                    <span class="text-tomato font-bold mt-0.5">&bull;</span>
                                    <span class="leading-relaxed" x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>

                    <!-- Recommendations -->
                    <div class="p-5 rounded-2xl bg-mint/25 border-2 border-ink">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-5 h-5 rounded-md bg-mint text-ink flex items-center justify-center font-bold text-xs border border-ink">&#10003;</span>
                            <h4 class="font-display font-extrabold text-sm text-ink uppercase tracking-wide">Cadangan Tindakan Intervensi</h4>
                        </div>
                        <ul class="space-y-2 text-xs text-slate-800">
                            <template x-for="(item, idx) in recommendations" :key="idx">
                                <li class="flex items-start gap-2">
                                    <span class="text-emerald-700 font-bold mt-0.5">&bull;</span>
                                    <span class="leading-relaxed" x-text="item"></span>
                                </li>
                            </template>
                        </ul>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- Topic Diagnostic Table & Misconceptions -->
    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-extrabold text-ink">Analisis Topik & Rekod Jurang Pemahaman</h2>
            <p class="text-xs text-slate-500">Pecahan prestasi mengikut unit sukatan bersama corak kesilapan murid</p>
        </div>

        <div class="academic-card overflow-hidden bg-white border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-ink text-paper uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="py-3 px-4">Topik & Ringkasan</th>
                            <th class="py-3 px-4 text-center">Ketepatan</th>
                            <th class="py-3 px-4 text-center">Cubaan (B / S)</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4">Pelan Tindakan Ejen AI & Pelarasan Aras</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($topics as $topic)
                            @php
                                $acc = $topic->accuracy_percentage;
                                $statusBadge = match($topic->status) {
                                    'tewas' => ['bg-emerald-50 text-emerald-700 border-emerald-200', 'Dikuasai'],
                                    'boss' => ($topic->losses >= 2) 
                                        ? ['bg-rose-50 text-rose-700 border-rose-200', 'Kritikal / Perlu Bantuan Guru']
                                        : ['bg-amber-50 text-amber-800 border-amber-200', 'Cabaran Penguasaan'],
                                    default => ['bg-slate-50 text-slate-700 border-slate-200', 'Terkawal'],
                                };
                            @endphp
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-3.5 px-4 max-w-xs">
                                    <div class="font-bold text-slate-900 text-sm">{{ $topic->name }}</div>
                                    <p class="text-slate-500 text-[11px] mt-0.5 leading-relaxed">{{ $topic->summary }}</p>
                                    @if(!empty($topic->mistakes))
                                        <div class="mt-2 p-2 rounded-lg bg-rose-50/60 border border-rose-100 text-[11px] text-rose-800 space-y-1">
                                            <span class="font-bold block">Soalan Lazim Silap:</span>
                                            <ul class="list-disc list-inside space-y-0.5">
                                                @foreach(array_slice($topic->mistakes, -3) as $m)
                                                    <li class="truncate" title="{{ $m }}">{{ $m }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono font-bold text-sm">
                                    <span class="{{ $acc >= 75 ? 'text-emerald-600' : ($acc >= 50 ? 'text-indigo-600' : 'text-rose-600') }}">
                                        {{ $acc }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-mono text-slate-600">
                                    <span class="text-emerald-700 font-bold">{{ $topic->right_count }}</span> / 
                                    <span class="text-rose-700 font-bold">{{ $topic->wrong_count }}</span>
                                    <div class="text-[10px] text-slate-400">Jum: {{ $topic->attempts }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 rounded-md text-[10px] font-bold border-2 {{ $statusBadge[0] }}">
                                        {{ $statusBadge[1] }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-700 leading-relaxed">
                                    <div class="font-medium text-[11px] text-indigo-900">
                                        Aras: {{ $topic->strategy['level'] ?? 2 }} &bull; Petunjuk: {{ ($topic->strategy['hint'] ?? false) ? 'Aktif' : 'Tidak Aktif' }}
                                    </div>
                                    <p class="text-[11px] text-slate-600 mt-0.5">
                                        {{ $topic->strategy['note'] ?? 'Sistem mengekalkan aras lazim.' }}
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- Prescribed Interventions & Teacher Recommendations -->
    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-extrabold text-ink">Pelan Tindakan Ejen AI & Cadangan Intervensi Guru</h2>
            <p class="text-xs text-slate-500">Garis panduan tindakan pedagogi untuk guru berdasarkan corak jawapan pelajar</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="academic-card p-5 bg-white border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2 text-indigo-900 font-bold text-sm">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Strategi Pengukuhan Kendiri Pelajar</span>
                </div>
                <ul class="text-xs text-slate-600 space-y-2 leading-relaxed">
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">&bull;</span>
                        <span>Bagi topik dengan ketepatan bawah 50%, pelajar diarahkan menggunakan <strong>Flashcard Berjarak</strong> untuk mengukuhkan ingatan fakta asas sebelum penilaian penguasaan.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">&bull;</span>
                        <span>Bagi topik sederhana (50% - 75%), aktiviti <strong>Padanan Konsep</strong> dan <strong>Menara Kefahaman</strong> digunakan bagi membina perkaitan istilah dan hubungan sebab-akibat.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-indigo-600 font-bold">&bull;</span>
                        <span>Pelajar yang mencapai ketepatan melebihi 80% dicabar melalui <strong>Ujian Pantas (Blitz 60)</strong> dan <strong>Penanda Aras Adaptif</strong> untuk membina kelancaran mental.</span>
                    </li>
                </ul>
            </div>

            <div class="academic-card p-5 bg-white border border-slate-200 space-y-3">
                <div class="flex items-center space-x-2 text-indigo-900 font-bold text-sm">
                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>Tindakan Pengajar / Guru di Bilik Darjah</span>
                </div>
                <div class="text-xs text-slate-600 space-y-2 leading-relaxed">
                    @php
                        $criticalTopics = $topics->filter(fn($t) => $t->losses >= 2 || $t->accuracy_percentage < 40);
                    @endphp
                    @if($criticalTopics->isNotEmpty())
                        <div class="p-3 rounded-lg bg-amber-50 border border-amber-200 text-amber-900">
                            <strong>Perhatian Guru Diperlukan:</strong> Topik berikut dikesan mempunyai kadar kekeliruan berulang:
                            <ul class="list-disc list-inside mt-1 font-medium">
                                @foreach($criticalTopics as $ct)
                                    <li>{{ $ct->name }} (Ketepatan {{ $ct->accuracy_percentage }}%, {{ $ct->losses }} kekalahan)</li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-emerald-800 bg-emerald-50 p-3 rounded-lg border border-emerald-200 font-medium">
                            Tiada topik kritikal dikesan. Pelajar menunjukkan pemahaman yang memuaskan merentasi semua topik sukatan.
                        </p>
                    @endif
                    <p>
                        Cadangan: Gunakan soalan rujukan daripada teks sumber semasa sesi perbincangan kelas untuk membetulkan salah tanggapan istilah.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Recent Study Sessions Audit Log -->
    <section class="space-y-4">
        <div>
            <h2 class="text-2xl font-extrabold text-ink">Log Aktiviti Sesi Terkini</h2>
            <p class="text-xs text-slate-500">Rekod sesi interaktif yang telah diselesaikan</p>
        </div>

        <div class="academic-card overflow-hidden bg-white border border-slate-200">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-ink text-paper uppercase tracking-wider font-bold text-[11px]">
                        <tr>
                            <th class="py-2.5 px-4">Tarikh & Masa</th>
                            <th class="py-2.5 px-4">Jenis Aktiviti</th>
                            <th class="py-2.5 px-4">Topik</th>
                            <th class="py-2.5 px-4 text-center">Mata</th>
                            <th class="py-2.5 px-4 text-center">Betul / Salah</th>
                            <th class="py-2.5 px-4 text-center">Combo Terbaik</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-mono">
                        @forelse($recentSessions as $sess)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-2.5 px-4 text-slate-500 font-sans">{{ $sess->created_at->format('d M, H:i') }}</td>
                                <td class="py-2.5 px-4 font-sans font-semibold text-slate-800 uppercase text-[11px]">{{ $sess->activity_type }}</td>
                                <td class="py-2.5 px-4 font-sans text-slate-600">{{ $sess->topic?->name ?? 'Semua Topik' }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-slate-900">{{ $sess->score }}</td>
                                <td class="py-2.5 px-4 text-center">
                                    <span class="text-emerald-700 font-bold">{{ $sess->right_count }}</span> / 
                                    <span class="text-rose-700 font-bold">{{ $sess->wrong_count }}</span>
                                </td>
                                <td class="py-2.5 px-4 text-center text-slate-600">x{{ $sess->best_combo }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-slate-400 font-sans">
                                    Tiada sesi latihan direkodkan lagi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<script>
function teacherAiReport(config) {
    return {
        status: config.initialStatus,
        summary: config.initialSummary,
        misconceptions: config.initialMisconceptions,
        recommendations: config.initialRecommendations,
        hasSessions: config.hasSessions,
        loading: false,
        pollTimer: null,

        get statusBadgeText() {
            switch (this.status) {
                case 'completed': return 'Analisis Sedia & Aktif';
                case 'processing': return 'Sedang Memproses...';
                case 'pending': return 'Dalam Giliran...';
                case 'failed': return 'Gagal Dijana';
                default: return 'Belum Dijana';
            }
        },

        init() {
            if (this.status === 'processing' || this.status === 'pending') {
                this.startPolling();
            }
        },

        async generateReport() {
            if (!this.hasSessions) return;
            this.loading = true;
            this.status = 'pending';

            try {
                const res = await fetch(config.generateUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (res.ok) {
                    this.startPolling();
                } else {
                    const err = await res.json();
                    alert(err.message || 'Gagal menghantar permintaan analisis.');
                    this.status = 'failed';
                }
            } catch (e) {
                this.status = 'failed';
            } finally {
                this.loading = false;
            }
        },

        startPolling() {
            if (this.pollTimer) clearInterval(this.pollTimer);

            this.pollTimer = setInterval(async () => {
                try {
                    const res = await fetch(config.statusUrl, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!res.ok) return;

                    const data = await res.json();
                    this.status = data.status;

                    if (data.status === 'completed') {
                        clearInterval(this.pollTimer);
                        this.summary = data.summary;
                        this.misconceptions = data.misconceptions || [];
                        this.recommendations = data.recommendations || [];
                    } else if (data.status === 'failed') {
                        clearInterval(this.pollTimer);
                    }
                } catch (e) {
                    console.error('Report status polling error:', e);
                }
            }, 2500);
        }
    };
}
</script>
@endsection
