<!DOCTYPE html>
<html lang="ms" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#fbf4ea">

    <link rel="icon" href="{{ asset('favicon.ico') }}?v=syed" sizes="48x48">
    <link rel="icon" href="{{ asset('favicon.svg') }}?v=syed" type="image/svg+xml">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}?v=syed">

    <title>{{ $title ?? 'KwizLoo' }} — KwizLoo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,500;12..96,700;12..96,800&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col text-slate-800" x-data="{ drawerOpen: false, soundEnabled: true }">
    <!-- Top Navigation -->
    <header class="sticky top-0 z-40 bg-paper/90 backdrop-blur border-b-2 border-ink">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            <div class="flex items-center gap-5 min-w-0">
                <a href="{{ isset($deck) ? route('decks.show', $deck) : route('home') }}" class="flex items-center gap-2.5 group shrink-0">
                    <span class="block w-10 h-10 -rotate-3 group-hover:rotate-3 transition-transform duration-200">
                        @include('partials.syed-avatar', ['class' => 'w-10 h-10'])
                    </span>
                    <span class="font-display text-xl font-extrabold tracking-tight text-ink">
                        Kwiz<span class="text-indigo-600">loo</span>
                    </span>
                </a>

                @if(isset($deck))
                    <div class="hidden lg:flex items-center gap-2 text-sm min-w-0">
                        <span class="text-slate-300">/</span>
                        <span class="text-slate-700 font-semibold truncate max-w-[260px]">{{ $deck->title }}</span>
                    </div>
                @endif
            </div>

            <nav class="flex items-center gap-1.5 sm:gap-2">
                @if(isset($deck))
                    <a href="{{ route('decks.show', $deck) }}"
                       class="hidden sm:inline-flex text-sm font-semibold px-3 py-1.5 rounded-lg transition {{ request()->routeIs('decks.show') ? 'bg-ink text-paper' : 'text-slate-700 hover:bg-white hover:text-ink' }}">
                        Hab Pembelajaran
                    </a>
                    <a href="{{ route('decks.report', $deck) }}"
                       class="hidden sm:inline-flex text-sm font-semibold px-3 py-1.5 rounded-lg transition {{ request()->routeIs('decks.report') ? 'bg-ink text-paper' : 'text-slate-700 hover:bg-white hover:text-ink' }}">
                        Laporan Guru
                    </a>
                @endif

                <button
                    type="button"
                    @click="$dispatch('open-onboarding')"
                    class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-bold px-2.5 sm:px-3 py-1.5 rounded-lg text-slate-700 hover:text-ink hover:bg-white border-2 border-transparent hover:border-ink hover:shadow-pop-sm transition"
                    title="Buka panduan langkah demi langkah"
                    aria-label="Buka panduan"
                >
                    <svg class="w-4 h-4 text-sun fill-sun stroke-ink" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    <span class="hidden sm:inline">Panduan</span>
                </button>

                <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white px-3 sm:px-3.5 py-1.5 text-sm whitespace-nowrap shrink-0" aria-label="Nota Baharu">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                    <span class="{{ isset($deck) ? 'hidden min-[420px]:inline' : '' }}">Nota Baharu</span>
                </a>

                @if(isset($deck))
                    <button
                        @click="drawerOpen = !drawerOpen"
                        type="button"
                        class="relative p-2 rounded-lg text-slate-700 hover:bg-white transition"
                        title="Log Keputusan Ejen AI"
                        aria-label="Buka log keputusan ejen AI"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h10M4 18h7"/></svg>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 rounded-full bg-mint border-2 border-paper"></span>
                    </button>
                @endif

                <button
                    @click="soundEnabled = !soundEnabled; window.AcademicAudio.enabled = soundEnabled"
                    type="button"
                    class="p-2 rounded-lg text-slate-700 hover:bg-white transition"
                    :title="soundEnabled ? 'Bunyi aktif' : 'Bunyi dimatikan'"
                    :aria-label="soundEnabled ? 'Matikan bunyi' : 'Hidupkan bunyi'"
                >
                    <svg x-show="soundEnabled" class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5zM15.5 8.5a5 5 0 0 1 0 7M18.5 5.5a9 9 0 0 1 0 13"/></svg>
                    <svg x-show="!soundEnabled" class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5 6 9H3v6h3l5 4V5zM22 9l-6 6M16 9l6 6"/></svg>
                </button>
            </nav>
        </div>
    </header>

    @if(session('status'))
        <div class="max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-mint/20 border-2 border-ink shadow-pop-sm text-ink text-sm font-medium px-4 py-3 rounded-xl flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-ink/60 hover:text-ink text-lg font-bold leading-none" aria-label="Tutup">×</button>
            </div>
        </div>
    @endif

    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    @if(isset($deck))
        <div x-show="drawerOpen" @click="drawerOpen = false" x-transition.opacity class="fixed inset-0 z-40 bg-ink/30" style="display: none;"></div>
        <aside
            x-show="drawerOpen"
            @keydown.escape.window="drawerOpen = false"
            x-transition:enter="transform transition ease-out duration-250"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 max-w-md w-full bg-paper border-l-2 border-ink z-50 overflow-y-auto p-6"
            style="display: none;"
        >
            <div class="flex items-start justify-between pb-4 border-b-2 border-dashed border-slate-300 mb-6">
                <div>
                    <h3 class="text-lg font-extrabold text-ink">Log Keputusan Ejen AI</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Rekod analisis dan keputusan ejen AI berdasarkan prestasi anda.</p>
                </div>
                <button @click="drawerOpen = false" type="button" class="text-slate-500 hover:text-ink p-1.5 rounded-lg hover:bg-white" aria-label="Tutup">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>

            @php
                $logStyles = [
                    'Keputusan' => 'bg-indigo-600 text-white',
                    'Fikir' => 'bg-sun text-ink',
                    'Semakan' => 'bg-mint text-ink',
                    'Ralat' => 'bg-tomato text-white',
                ];
            @endphp

            <ol class="relative space-y-4 border-l-2 border-ink/15 ml-2">
                @forelse($deck->agentLogs->take(15) as $log)
                    <li class="pl-5 relative">
                        <span class="absolute -left-[7px] top-4 w-3 h-3 rounded-full border-2 border-ink {{ $logStyles[$log->kind] ?? 'bg-white' }}"></span>
                        <div class="p-3.5 rounded-xl border-2 border-ink bg-white text-sm">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md {{ $logStyles[$log->kind] ?? 'bg-slate-100 text-slate-700' }}">{{ $log->kind }}</span>
                                <span class="text-slate-400 text-[11px]">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-slate-700 leading-relaxed text-[13px]">{{ $log->message }}</p>
                            @if($log->topic)
                                <div class="mt-2 text-[11px] text-slate-500 font-semibold">Topik: {{ $log->topic->name }}</div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="pl-5 text-sm text-slate-500 py-8">Belum ada rekod. Selesaikan satu aktiviti untuk bermula.</li>
                @endforelse
            </ol>
        </aside>
    @endif

    <footer class="mt-auto border-t-2 border-ink bg-ink text-paper/70 py-6 text-xs print:hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                @include('partials.syed-avatar', ['class' => 'w-6 h-6'])
                <span class="font-display font-extrabold text-sm text-paper">Kwiz<span class="text-sun">loo</span></span>
                <span>— Pembelajaran interaktif berkuasa AI, berasaskan nota anda.</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-mint"></span>Soalan dijana AI daripada nota anda</span>
                <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-sun"></span>Setiap jawapan disokong petikan sumber</span>
            </div>
        </div>
    </footer>

    @include('partials.onboarding-modal')

    @stack('scripts')
</body>
</html>
