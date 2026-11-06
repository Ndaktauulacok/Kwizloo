@extends('layouts.app', ['title' => 'Cipta Modul Pembelajaran'])

@section('content')
<div class="max-w-4xl mx-auto py-4">
    <!-- Academic Header Banner -->
    <div class="text-center mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 mb-3 shadow-2xs">
            <svg class="w-3.5 h-3.5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            Sistem Pembelajaran Berasaskan Bukti
        </span>
        <h1 class="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900 font-sans">
            Masukkan Nota Pembelajaran
        </h1>
        <p class="mt-2 text-base text-slate-600 max-w-2xl mx-auto">
            Tampal teks sukatan pelajaran atau muat naik dokumen PDF. Ejen pedagogi akan mengasingkan topik utama, menguji kefahaman anda, dan mengubah jurang kelemahan menjadi cabaran penguasaan.
        </p>
    </div>

    <!-- Intake Card -->
    <div class="academic-card p-6 sm:p-8 bg-white border border-slate-200 shadow-sm" x-data="{ isDragging: false, activeTab: 'paste' }">
        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                <div class="font-semibold mb-1">Perhatian Diperlukan:</div>
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('decks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="block text-sm font-semibold text-slate-800 mb-1.5">
                    Tajuk Modul (Pilihan)
                </label>
                <input 
                    type="text" 
                    id="title" 
                    name="title" 
                    value="{{ old('title') }}" 
                    placeholder="cth: Sains Tingkatan 4 — Bab 2: Struktur Sel"
                    class="w-full px-4 py-2.5 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-sm text-slate-900 placeholder:text-slate-400 outline-none transition"
                >
            </div>

            <!-- Input Tabs -->
            <div class="flex items-center space-x-2 border-b border-slate-200 pb-2">
                <button 
                    type="button" 
                    @click="activeTab = 'paste'"
                    :class="activeTab === 'paste' ? 'text-indigo-900 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 border-transparent hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium border-b-2 rounded-t-lg transition flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span>Tampal Nota Teks</span>
                </button>
                <button 
                    type="button" 
                    @click="activeTab = 'upload'"
                    :class="activeTab === 'upload' ? 'text-indigo-900 border-indigo-600 bg-indigo-50/50' : 'text-slate-500 border-transparent hover:text-slate-700'"
                    class="px-4 py-2 text-sm font-medium border-b-2 rounded-t-lg transition flex items-center space-x-2"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                    <span>Muat Naik Dokumen (PDF / TXT)</span>
                </button>
            </div>

            <!-- Text Area Tab -->
            <div x-show="activeTab === 'paste'">
                <label for="note_text" class="block text-sm font-medium text-slate-700 mb-1.5">
                    Kandungan Nota
                </label>
                <textarea 
                    id="note_text" 
                    name="note_text" 
                    rows="8" 
                    placeholder="Tampal teks sukatan pelajaran, artikel rujukan, atau nota ringkas di sini (sekurang-kurangnya 80 aksara)..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-indigo-600 focus:ring-2 focus:ring-indigo-100 text-sm text-slate-800 placeholder:text-slate-400 font-mono leading-relaxed outline-none transition"
                >{{ old('note_text') }}</textarea>
                <p class="text-xs text-slate-500 mt-1.5">
                    Kandungan teks akan dianalisis secara selamat. Setiap soalan yang dijana akan diikat terus kepada petikan teks sumber bagi mengelakkan maklumat palsu.
                </p>
            </div>

            <!-- File Upload Tab -->
            <div x-show="activeTab === 'upload'" style="display: none;">
                <div 
                    class="border-2 border-dashed rounded-xl p-8 text-center transition cursor-pointer"
                    :class="isDragging ? 'border-indigo-500 bg-indigo-50/50' : 'border-slate-300 bg-slate-50/50 hover:bg-slate-50'"
                    @dragover.prevent="isDragging = true"
                    @dragleave.prevent="isDragging = false"
                    @drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files"
                    @click="$refs.fileInput.click()"
                >
                    <svg class="mx-auto h-12 w-12 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-sm font-semibold text-slate-700">Pilih fail PDF atau TXT untuk dimuat naik</p>
                    <p class="text-xs text-slate-500 mt-1">Seret dan lepas di sini (Maksimum 10MB)</p>
                    <input 
                        type="file" 
                        name="document" 
                        x-ref="fileInput" 
                        accept=".pdf,.txt,application/pdf,text/plain" 
                        class="hidden"
                    >
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-4 border-t border-slate-200">
                <button 
                    type="submit" 
                    name="use_sample" 
                    value="1" 
                    class="w-full sm:w-auto px-5 py-2.5 rounded-xl border border-emerald-300 bg-emerald-50 hover:bg-emerald-100/80 text-emerald-900 text-sm font-bold transition shadow-2xs flex items-center justify-center space-x-2"
                >
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <span>Gunakan Nota Contoh: Fotosintesis</span>
                </button>

                <button 
                    type="submit" 
                    class="w-full sm:w-auto px-6 py-2.5 rounded-xl bg-gradient-to-r from-indigo-700 via-indigo-800 to-violet-800 hover:from-indigo-600 hover:to-violet-700 text-white text-sm font-bold transition shadow-md hover:shadow-lg flex items-center justify-center space-x-2"
                >
                    <span>Bina Modul Pembelajaran</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Security & Evidence Callout -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4 text-xs text-slate-600">
        <div class="p-4 rounded-2xl border border-emerald-200/80 bg-gradient-to-b from-white to-emerald-50/20 shadow-2xs">
            <div class="font-bold text-emerald-950 mb-1.5 flex items-center gap-1.5">
                <span class="p-1 rounded-md bg-emerald-100 text-emerald-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </span>
                <span>Integriti & Bukti Teks</span>
            </div>
            Setiap soalan dan kad disemak secara ketat terhadap petikan verbatim sumber nota. Tiada maklumat luar yang tidak disahkan.
        </div>
        <div class="p-4 rounded-2xl border border-violet-200/80 bg-gradient-to-b from-white to-violet-50/20 shadow-2xs">
            <div class="font-bold text-violet-950 mb-1.5 flex items-center gap-1.5">
                <span class="p-1 rounded-md bg-violet-100 text-violet-700">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </span>
                <span>Ejen Pedagogi Adaptif</span>
            </div>
            Sistem mengesan soalan yang silap dijawab, menukarnya menjadi cabaran penguasaan, dan memilih aktiviti sesuai mengikut tahap kognitif.
        </div>
        <div class="p-4 rounded-2xl border border-amber-200/80 bg-gradient-to-b from-white to-amber-50/20 shadow-2xs">
            <div class="font-bold text-amber-950 mb-1.5 flex items-center gap-1.5">
                <span class="p-1 rounded-md bg-amber-100 text-amber-800">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </span>
                <span>Laporan Diagnostik Guru</span>
            </div>
            Semua sesi disimpan ke pangkalan data selamat dengan analisis ketepatan topik dan cadangan intervensi untuk pengajar.
        </div>
    </div>
</div>
@endsection
