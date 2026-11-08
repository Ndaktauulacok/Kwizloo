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

        <div id="error-container" class="hidden mt-6 p-4 rounded-xl bg-tomato/15 border-2 border-tomato text-tomato text-sm font-bold">
            <p id="error-message"></p>
            <a href="{{ route('decks.create') }}" class="mt-3 inline-block px-4 py-2 bg-ink text-white rounded-xl text-xs font-bold hover:bg-slate-800">
                Cuba Semula
            </a>
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
                errorContainer.classList.remove('hidden');
                errorMessage.textContent = data.error || 'Ralat berlaku semasa penjanaan AI. Sila cuba lagi.';
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }, 2000);
});
</script>
@endsection
