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
            <span class="text-[11px] uppercase font-bold text-ink/70 block tracking-wider">Intervensi Ejen</span>
            <div class="text-4xl font-extrabold font-display text-indigo-700 mt-1">
                {{ $logs->count() }}
            </div>
            <span class="text-xs text-ink/70 mt-0.5 block">Pelarasan pedagogi automatik</span>
        </div>
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
                            <th class="py-3 px-4">Pelan Tindakan Ejen & Pelarasan Aras</th>
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
            <h2 class="text-2xl font-extrabold text-ink">Pelan Tindakan Ejen & Cadangan Intervensi Guru</h2>
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
@endsection
