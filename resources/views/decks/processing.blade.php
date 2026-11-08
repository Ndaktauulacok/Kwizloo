@extends('layouts.app', ['title' => 'Menjana Modul AI - ' . $deck->title])

@section('content')
<div class="max-w-xl mx-auto py-12 px-4 text-center">
    <div class="card p-8 sm:p-10 bg-white border-3 border-ink shadow-pop rounded-3xl relative overflow-hidden">
        <!-- Floating badge -->
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-sun border-2 border-ink text-xs font-black tracking-wide text-ink uppercase mb-6 animate-bounce">
            <span class="w-2.5 h-2.5 rounded-full bg-tomato animate-ping"></span>
            Gemini AI Sedang Memproses
        </div>

        <h1 class="font-display font-extrabold text-2xl sm:text-3xl text-ink leading-tight">
            Menyediakan Modul Pembelajaran
        </h1>

        <p class="mt-2 text-sm text-slate-600 font-medium line-clamp-1">
            "{{ $deck->title }}"
        </p>

        <!-- Dynamic Step Container -->
        <div class="my-8 py-6 px-4 bg-sky-pop/20 border-2 border-ink rounded-2xl">
            <div class="flex items-center justify-center gap-3">
                <svg class="animate-spin h-6 w-6 text-ink" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span id="step-label" class="font-bold text-base text-ink">Menganalisis teks nota & bab teras...</span>
            </div>

            <!-- Progress Bar -->
            <div class="mt-5 w-full bg-white rounded-full h-4 border-2 border-ink overflow-hidden p-0.5">
                <div id="progress-bar" class="bg-mint h-full rounded-full transition-all duration-500 ease-out" style="width: {{ $deck->progress }}%;"></div>
            </div>
            <div class="mt-2 text-xs font-black text-ink/70" id="progress-pct">{{ $deck->progress }}% Selesai</div>
        </div>

        <p class="text-xs text-slate-500 max-w-sm mx-auto">
            AI sedang mengekstrak konsep penting, menjana soalan aneka pilihan, dan menyediakan kad imbasan secara automatik. Sila tunggu sebentar.
        </p>

        <!-- Polished Error State (Hidden by default) -->
        <div id="error-container" class="hidden mt-6 text-left bg-white border-2 border-tomato rounded-2xl p-6 shadow-pop-sm">
            <div class="flex items-start gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-tomato/20 border-2 border-tomato flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5 text-tomato" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </div>
                <div class="flex-1">
                    <div class="inline-block px-2 py-0.5 rounded-md bg-tomato text-white text-[11px] font-black uppercase tracking-wider mb-1" id="error-badge">
                        Penjanaan AI Tergendala
                    </div>
                    <h3 class="font-display font-extrabold text-base text-ink" id="error-title">Penjanaan AI Tergendala</h3>
                    <p class="text-xs text-slate-700 mt-1 leading-relaxed" id="error-message"></p>
                </div>
            </div>

            <!-- Alternative Guidance Box -->
            <div class="mt-4 p-3.5 rounded-xl bg-sun/25 border-2 border-ink text-xs text-ink leading-relaxed">
                <span class="font-bold">💡 Alternatif Serta-Merta:</span> Anda boleh menjana modul ini sekarang juga dalam <strong>Mod Asas (Luar Talian)</strong>. Sistem akan mengekstrak soalan kuiz dan kad imbasan terus daripada teks nota anda tanpa menggunakan sebarang kuota AI.
            </div>

            <!-- Action buttons -->
            <div class="mt-5 flex flex-col sm:flex-row gap-2.5">
                <form action="{{ route('decks.retry-fallback', $deck) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="w-full btn-pop bg-mint text-ink font-bold px-4 py-2.5 text-xs rounded-xl flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        <span>Jana Dalam Mod Asas Segera</span>
                    </button>
                </form>

                <a href="{{ route('decks.create') }}" class="btn-pop bg-slate-100 hover:bg-white text-slate-700 font-bold px-4 py-2.5 text-xs rounded-xl flex items-center justify-center gap-1.5 border border-ink">
                    <span>Kembali / Cuba Semula</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const statusUrl = "{{ route('decks.status', $deck) }}";
    const progressBar = document.getElementById('progress-bar');
    const progressPct = document.getElementById('progress-pct');
    const stepLabel = document.getElementById('step-label');
    const errorContainer = document.getElementById('error-container');
    const errorMessage = document.getElementById('error-message');
    const errorTitle = document.getElementById('error-title');
    const errorBadge = document.getElementById('error-badge');

    let pollInterval = setInterval(async () => {
        try {
            const res = await fetch(statusUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!res.ok) {
                return;
            }

            const data = await res.json();

            if (progressBar && progressPct) {
                progressBar.style.width = data.progress + '%';
                progressPct.textContent = data.progress + '% Selesai';
            }

            if (data.progress > 20 && data.progress < 70) {
                stepLabel.textContent = 'Membina soalan kuiz & kad imbasan teras...';
            } else if (data.progress >= 70 && data.progress < 100) {
                stepLabel.textContent = 'Memverifikasi rujukan petikan teks dan ketepatan...';
            }

            if (data.status === 'completed') {
                clearInterval(pollInterval);
                stepLabel.textContent = 'Selesai! Mengalihkan ke hab pembelajaran...';
                setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 600);
            } else if (data.status === 'failed') {
                clearInterval(pollInterval);
                stepLabel.textContent = 'Penjanaan terhenti';
                errorContainer.classList.remove('hidden');

                let cleanError = data.error || 'Ralat berlaku semasa penjanaan modul AI.';
                const lower = cleanError.toLowerCase();

                if (lower.includes('429') || lower.includes('quota') || lower.includes('resource_exhausted') || lower.includes('{')) {
                    if (errorBadge) errorBadge.textContent = 'Kuota AI Dicapai';
                    if (errorTitle) errorTitle.textContent = 'Had Kuota Harian AI Percuma Telah Dicapai';
                    cleanError = 'Akaun percuma Google Gemini AI mengehadkan 20 permintaan setiap hari dan had tersebut telah dicapai buat masa ini. Sila klik "Jana Dalam Mod Asas Segera" di bawah untuk menghasilkan modul pembelajaran anda tanpa kuota AI.';
                } else {
                    if (errorBadge) errorBadge.textContent = 'Ralat Penjanaan';
                    if (errorTitle) errorTitle.textContent = 'Penjanaan AI Tidak Berjaya';
                }

                errorMessage.textContent = cleanError;
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }, 2000);
});
</script>
@endsection
