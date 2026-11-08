@extends('layouts.app', ['title' => 'Sejarah Nota'])

@section('content')
@php
    $statusStyles = [
        'completed' => ['label' => 'Siap', 'class' => 'bg-mint/30 text-ink'],
        'processing' => ['label' => 'Sedang diproses', 'class' => 'bg-sun/50 text-ink'],
        'pending' => ['label' => 'Dalam giliran', 'class' => 'bg-sun/50 text-ink'],
        'failed' => ['label' => 'Gagal dijana', 'class' => 'bg-tomato text-white'],
    ];
@endphp

<div
    class="max-w-5xl mx-auto"
    x-data="{
        deleteOpen: false,
        deleting: false,
        target: { title: '', url: '', topics: 0, questions: 0, flashcards: 0, sessions: 0 },
        askDelete(deck) {
            this.target = deck;
            this.deleting = false;
            this.deleteOpen = true;
            this.$nextTick(() => this.$refs.cancelDelete?.focus());
        },
        closeDelete() {
            if (this.deleting) return;
            this.deleteOpen = false;
        },
    }"
    x-effect="document.documentElement.classList.toggle('overflow-hidden', deleteOpen)"
    @keydown.escape.window="closeDelete()"
>
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-5">
        <div class="flex items-center gap-4 min-w-0">
            @include('partials.fahim-avatar', ['class' => 'w-14 h-14 shrink-0'])
            <div class="min-w-0">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-ink">Sejarah Nota</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Semua nota yang pernah diproses. Buka semula mana-mana modul untuk sambung belajar.
                </p>
            </div>
        </div>

        <a href="{{ route('decks.create') }}" class="btn-pop bg-indigo-600 text-white text-sm px-5 py-2.5 shrink-0 self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
            <span>Nota Baharu</span>
        </a>
    </div>

    <form action="{{ route('decks.index') }}" method="GET" class="mt-8 flex gap-2" role="search">
        <label for="history-search" class="sr-only">Cari tajuk nota</label>
        <input
            id="history-search"
            type="search"
            name="q"
            value="{{ $search }}"
            placeholder="Cari tajuk nota, contohnya Fotosintesis"
            class="flex-1 min-w-0 rounded-xl border-2 border-ink bg-white px-4 py-2.5 text-sm text-ink placeholder:text-slate-400 shadow-pop-sm focus:outline-none focus-visible:ring-0"
        >
        <button type="submit" class="btn-pop bg-white text-ink text-sm px-4 py-2.5">Cari</button>
        @if($search !== '')
            <a href="{{ route('decks.index') }}" class="btn-pop bg-paper text-slate-600 text-sm px-4 py-2.5">Padam</a>
        @endif
    </form>

    @if($decks->isEmpty())
        <div class="mt-10 academic-card p-10 text-center">
            @if($search !== '')
                <h2 class="text-xl font-extrabold text-ink">Tiada nota bertajuk "{{ $search }}"</h2>
                <p class="mt-2 text-sm text-slate-600">Cuba kata kunci lain, atau padam carian untuk melihat semua nota.</p>
            @else
                <h2 class="text-xl font-extrabold text-ink">Belum ada nota diproses</h2>
                <p class="mt-2 text-sm text-slate-600">Muat naik nota pertama anda. Ia akan disimpan di sini supaya anda boleh kembali bila-bila masa.</p>
                <a href="{{ route('decks.create') }}" class="mt-5 btn-pop bg-indigo-600 text-white text-sm px-5 py-2.5">Muat Naik Nota</a>
            @endif
        </div>
    @else
        <p class="mt-6 text-xs font-bold uppercase tracking-wider text-slate-500 tabular-nums">
            {{ $decks->total() }} nota
        </p>

        <ul class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($decks as $historyDeck)
                @php
                    $status = $statusStyles[$historyDeck->status] ?? $statusStyles['completed'];
                    $isReady = $historyDeck->status === 'completed';
                @endphp
                <li class="academic-card p-5 flex flex-col gap-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h2 class="text-lg font-extrabold text-ink leading-snug break-words">
                                <a href="{{ $isReady ? route('decks.show', $historyDeck) : route('decks.processing', $historyDeck) }}" class="hover:underline decoration-2 decoration-sun underline-offset-4">
                                    {{ $historyDeck->title }}
                                </a>
                            </h2>
                            <p class="mt-1 text-xs text-slate-500">
                                <time datetime="{{ $historyDeck->created_at?->toIso8601String() }}">{{ $historyDeck->created_at?->format('j/n/Y, g:i A') }}</time>
                                · {{ $historyDeck->mode === 'sim' ? 'Pek contoh' : 'Dijana AI' }}
                            </p>
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-md border border-ink text-[11px] font-extrabold {{ $status['class'] }}">
                            {{ $status['label'] }}
                        </span>
                    </div>

                    <dl class="grid grid-cols-4 gap-2 text-center">
                        @foreach([
                            ['value' => $historyDeck->topics_count, 'label' => 'topik'],
                            ['value' => $historyDeck->questions_count, 'label' => 'soalan'],
                            ['value' => $historyDeck->flashcards_count, 'label' => 'kad imbas'],
                            ['value' => $historyDeck->study_sessions_count, 'label' => 'sesi'],
                        ] as $stat)
                            <div class="rounded-lg bg-paper border border-ink/15 py-2 flex flex-col-reverse">
                                <dt class="text-[10px] font-semibold text-slate-500">{{ $stat['label'] }}</dt>
                                <dd class="font-display text-lg font-extrabold text-ink tabular-nums">{{ $stat['value'] }}</dd>
                            </div>
                        @endforeach
                    </dl>

                    <div class="mt-auto flex flex-wrap gap-2">
                        @if($isReady)
                            <a href="{{ route('decks.show', $historyDeck) }}" class="btn-pop bg-indigo-600 text-white text-xs px-4 py-2">Buka Hab Pembelajaran</a>
                            <a href="{{ route('decks.report', $historyDeck) }}" class="btn-pop bg-white text-ink text-xs px-4 py-2">Laporan Guru</a>
                        @else
                            <a href="{{ route('decks.processing', $historyDeck) }}" class="btn-pop bg-sun text-ink text-xs px-4 py-2">
                                {{ $historyDeck->status === 'failed' ? 'Lihat Ralat & Cuba Semula' : 'Lihat Status' }}
                            </a>
                        @endif

                        <button
                            type="button"
                            class="delete-trigger ml-auto inline-flex items-center gap-1.5 rounded-lg px-2.5 py-2 text-xs font-bold text-slate-500 hover:text-tomato hover:bg-tomato/10 transition"
                            @click="askDelete(@js([
                                'title' => $historyDeck->title,
                                'url' => route('decks.destroy', $historyDeck),
                                'topics' => $historyDeck->topics_count,
                                'questions' => $historyDeck->questions_count,
                                'flashcards' => $historyDeck->flashcards_count,
                                'sessions' => $historyDeck->study_sessions_count,
                            ]))"
                            aria-label="Padam {{ $historyDeck->title }}"
                        >
                            @include('partials.trash-icon', ['class' => 'w-4 h-4'])
                            <span>Padam</span>
                        </button>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-8">
            {{ $decks->links() }}
        </div>
    @endif

    @include('decks.partials.delete-dialog')
</div>
@endsection
