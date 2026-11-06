<!DOCTYPE html>
<html lang="ms" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'KwizLoo' }} — Platform Pembelajaran Adaptif</title>

    <!-- Academic Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Newsreader:ital,opsz,wght@0,6..72,400;0,6..72,500;1,6..72,400&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="h-full flex flex-col bg-slate-50 text-slate-800" x-data="{ drawerOpen: false, soundEnabled: true }">
    <!-- Top Academic Navigation -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center space-x-6">
                <a href="{{ isset($deck) ? route('decks.show', $deck) : route('decks.create') }}" class="flex items-center space-x-2.5 text-slate-900 group">
                    <span class="w-9 h-9 rounded-xl bg-indigo-900 text-white flex items-center justify-center font-bold text-lg shadow-xs group-hover:bg-indigo-800 transition">
                        <svg class="w-5 h-5 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </span>
                    <div>
                        <span class="text-base font-semibold tracking-tight block leading-tight">KwizLoo</span>
                        <span class="text-xs font-medium text-slate-400 block leading-none">Akademik & Adaptif</span>
                    </div>
                </a>

                @if(isset($deck))
                    <div class="hidden md:flex items-center space-x-2 text-sm text-slate-400 border-l border-slate-200 pl-6">
                        <span class="text-slate-600 font-medium truncate max-w-xs">{{ $deck->title }}</span>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full font-mono">{{ $deck->topics()->count() }} Topik</span>
                    </div>
                @endif
            </div>

            <nav class="flex items-center space-x-3">
                @if(isset($deck))
                    <a href="{{ route('decks.show', $deck) }}" class="text-xs sm:text-sm font-medium px-3 py-1.5 rounded-lg text-slate-600 hover:text-indigo-900 hover:bg-slate-100 transition">
                        Hab Pembelajaran
                    </a>
                    <a href="{{ route('decks.report', $deck) }}" class="text-xs sm:text-sm font-medium px-3 py-1.5 rounded-lg text-slate-600 hover:text-indigo-900 hover:bg-slate-100 transition">
                        Laporan Guru
                    </a>
                @endif

                <a href="{{ route('decks.create') }}" class="text-xs sm:text-sm font-medium px-3 py-1.5 rounded-lg text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition">
                    + Nota Baharu
                </a>

                <!-- Agent Decision Log Trigger -->
                @if(isset($deck))
                    <button 
                        @click="drawerOpen = !drawerOpen" 
                        type="button" 
                        class="relative p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition" 
                        title="Buka Log Keputusan Ejen"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-emerald-500 ring-2 ring-white"></span>
                    </button>
                @endif

                <!-- Audio Toggle -->
                <button 
                    @click="soundEnabled = !soundEnabled; window.AcademicAudio.enabled = soundEnabled" 
                    type="button" 
                    class="p-2 rounded-lg text-slate-500 hover:text-slate-700 hover:bg-slate-100 transition" 
                    :title="soundEnabled ? 'Bunyi Aktif' : 'Bunyi Dimatikan'"
                >
                    <span x-show="soundEnabled" class="text-sm">♪</span>
                    <span x-show="!soundEnabled" class="text-sm text-slate-400">×</span>
                </button>
            </nav>
        </div>
    </header>

    <!-- Flash Status Messages -->
    @if(session('status'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm px-4 py-3 rounded-xl flex items-center justify-between">
                <span>{{ session('status') }}</span>
                <button type="button" onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 text-sm font-bold">×</button>
            </div>
        </div>
    @endif

    <!-- Main View Content -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    <!-- Discrete Agent Decision Log Drawer -->
    @if(isset($deck))
        <aside 
            x-show="drawerOpen" 
            x-transition:enter="transform transition ease-in-out duration-300"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transform transition ease-in-out duration-200"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="fixed inset-y-0 right-0 max-w-md w-full bg-white shadow-2xl border-l border-slate-200 z-50 overflow-y-auto p-6"
            style="display: none;"
        >
            <div class="flex items-center justify-between pb-4 border-b border-slate-200 mb-6">
                <div>
                    <h3 class="text-base font-semibold text-slate-900">Log Keputusan Ejen Pedagogi</h3>
                    <p class="text-xs text-slate-500">Rekod penalaran adaptif berdasarkan pencapaian murid</p>
                </div>
                <button @click="drawerOpen = false" type="button" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="space-y-4">
                @forelse($deck->agentLogs->take(15) as $log)
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/70 text-xs">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="font-semibold uppercase tracking-wider text-[10px] 
                                {{ $log->kind === 'Keputusan' ? 'text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded' : '' }}
                                {{ $log->kind === 'Fikir' ? 'text-slate-600 bg-slate-200 px-2 py-0.5 rounded' : '' }}
                                {{ $log->kind === 'Semakan' ? 'text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded' : '' }}
                                {{ $log->kind === 'Ralat' ? 'text-rose-700 bg-rose-50 px-2 py-0.5 rounded' : '' }}
                            ">{{ $log->kind }}</span>
                            <span class="text-slate-400 font-mono text-[10px]">{{ $log->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-slate-700 leading-relaxed font-normal">{{ $log->message }}</p>
                        @if($log->topic)
                            <div class="mt-2 text-[11px] text-slate-500 font-medium">Topik: {{ $log->topic->name }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-sm text-slate-400 text-center py-8">Tiada tindakan direkodkan lagi.</p>
                @endforelse
            </div>
        </aside>
    @endif

    <!-- Academic Footer -->
    <footer class="mt-auto border-t border-slate-200/80 bg-white py-6 text-center text-xs text-slate-400">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center space-x-2">
                <span class="font-semibold text-slate-700">KwizLoo Academic</span>
                <span>— Pembelajaran Berasaskan Bukti & Nota Rujukan</span>
            </div>
            <div>
                <span class="text-slate-400">Pematuhan Pedagogi &bull; Tiada Halusinasi &bull; Soalan Terikat Petikan Sumber</span>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
