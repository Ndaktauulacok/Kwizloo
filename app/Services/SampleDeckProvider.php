<?php

declare(strict_types=1);

namespace App\Services;

class SampleDeckProvider
{
    public const SAMPLE_NOTE = <<<'NOTE'
Fotosintesis
Fotosintesis ialah proses tumbuhan hijau membuat makanan sendiri. Proses ini memerlukan karbon dioksida, air dan cahaya matahari. Hasil fotosintesis ialah glukosa dan oksigen.
Karbon dioksida masuk ke dalam daun melalui stoma. Air diserap oleh akar dan diangkut ke daun melalui xilem.
Fotosintesis berlaku di dalam kloroplas. Kloroplas mengandungi klorofil, iaitu pigmen hijau yang menyerap tenaga cahaya. Kebanyakan kloroplas terdapat di dalam sel palisad pada daun.
Kadar fotosintesis dipengaruhi oleh keamatan cahaya, kepekatan karbon dioksida dan suhu. Kadar fotosintesis meningkat apabila keamatan cahaya bertambah sehingga mencapai tahap maksimum. Suhu yang terlalu tinggi boleh merosakkan enzim dan menurunkan kadar fotosintesis.
Glukosa digunakan oleh tumbuhan sebagai sumber tenaga melalui respirasi. Glukosa berlebihan disimpan sebagai kanji. Oksigen yang dibebaskan digunakan oleh hidupan lain untuk bernafas.
NOTE;

    /**
     * @return array{
     *     title: string,
     *     note: string,
     *     topics: array<int, array{
     *         nama: string,
     *         ringkasan: string,
     *         soalan: array<int, array{soalan: string, pilihan: array<int, string>, jawapan: int, tahap: int, penjelasan: string, petikan: string}>,
     *         kad: array<int, array{depan: string, belakang: string, petikan: string}>
     *     }>
     * }
     */
    public static function getPhotosynthesisSample(): array
    {
        return [
            'title' => 'Biologi Tingkatan 4: Fotosintesis',
            'note' => self::SAMPLE_NOTE,
            'topics' => [
                [
                    'nama' => 'Bahan dan Hasil',
                    'ringkasan' => 'Apa yang diperlukan dan dihasilkan oleh proses fotosintesis',
                    'soalan' => [
                        [
                            'soalan' => 'Apakah hasil fotosintesis?',
                            'pilihan' => ['Glukosa dan oksigen', 'Air dan kanji', 'Karbon dioksida dan air', 'Oksigen dan klorofil'],
                            'jawapan' => 0,
                            'tahap' => 1,
                            'penjelasan' => 'Tumbuhan menghasilkan glukosa dan membebaskan oksigen.',
                            'petikan' => 'Hasil fotosintesis ialah glukosa dan oksigen',
                        ],
                        [
                            'soalan' => 'Antara berikut, yang manakah bukan keperluan fotosintesis?',
                            'pilihan' => ['Air', 'Cahaya matahari', 'Oksigen', 'Karbon dioksida'],
                            'jawapan' => 2,
                            'tahap' => 1,
                            'penjelasan' => 'Oksigen ialah hasil, bukan bahan yang diperlukan.',
                            'petikan' => 'memerlukan karbon dioksida, air dan cahaya matahari',
                        ],
                        [
                            'soalan' => 'Bagaimanakah karbon dioksida memasuki daun?',
                            'pilihan' => ['Melalui xilem', 'Melalui stoma', 'Melalui akar', 'Melalui kanji'],
                            'jawapan' => 1,
                            'tahap' => 2,
                            'penjelasan' => 'Stoma ialah liang pada daun untuk pertukaran gas.',
                            'petikan' => 'Karbon dioksida masuk ke dalam daun melalui stoma',
                        ],
                        [
                            'soalan' => 'Bagaimanakah air sampai ke daun?',
                            'pilihan' => ['Diserap akar, kemudian diangkut oleh xilem', 'Masuk melalui stoma bersama cahaya', 'Dihasilkan oleh kloroplas', 'Dibentuk daripada glukosa'],
                            'jawapan' => 0,
                            'tahap' => 2,
                            'penjelasan' => 'Akar menyerap air dan tisu xilem membawanya ke daun.',
                            'petikan' => 'Air diserap oleh akar dan diangkut ke daun melalui xilem',
                        ],
                        [
                            'soalan' => 'Tumbuhan menyimpan glukosa berlebihan dalam bentuk apakah?',
                            'pilihan' => ['Oksigen', 'Klorofil', 'Kanji', 'Enzim'],
                            'jawapan' => 2,
                            'tahap' => 3,
                            'penjelasan' => 'Glukosa berlebihan ditukar kepada kanji sebagai simpanan tenaga.',
                            'petikan' => 'Glukosa berlebihan disimpan sebagai kanji',
                        ],
                    ],
                    'kad' => [
                        ['depan' => 'Fotosintesis', 'belakang' => 'Proses tumbuhan hijau membuat makanan sendiri.', 'petikan' => 'proses tumbuhan hijau membuat makanan sendiri'],
                        ['depan' => 'Bahan fotosintesis', 'belakang' => 'Karbon dioksida, air dan cahaya matahari.', 'petikan' => 'memerlukan karbon dioksida, air dan cahaya matahari'],
                        ['depan' => 'Hasil fotosintesis', 'belakang' => 'Glukosa dan oksigen.', 'petikan' => 'Hasil fotosintesis ialah glukosa dan oksigen'],
                        ['depan' => 'Stoma', 'belakang' => 'Laluan karbon dioksida masuk ke dalam daun.', 'petikan' => 'Karbon dioksida masuk ke dalam daun melalui stoma'],
                    ],
                ],
                [
                    'nama' => 'Kloroplas dan Klorofil',
                    'ringkasan' => 'Tempat fotosintesis berlaku dan peranan pigmen hijau',
                    'soalan' => [
                        [
                            'soalan' => 'Di manakah fotosintesis berlaku?',
                            'pilihan' => ['Di dalam kloroplas', 'Di dalam xilem', 'Di dalam stoma', 'Di dalam akar'],
                            'jawapan' => 0,
                            'tahap' => 1,
                            'penjelasan' => 'Kloroplas ialah organel tempat berlakunya fotosintesis.',
                            'petikan' => 'Fotosintesis berlaku di dalam kloroplas',
                        ],
                        [
                            'soalan' => 'Apakah fungsi klorofil?',
                            'pilihan' => ['Mengangkut air', 'Menyerap tenaga cahaya', 'Menyimpan kanji', 'Membebaskan glukosa'],
                            'jawapan' => 1,
                            'tahap' => 1,
                            'penjelasan' => 'Klorofil ialah pigmen hijau yang bertindak memerangkap cahaya.',
                            'petikan' => 'pigmen hijau yang menyerap tenaga cahaya',
                        ],
                        [
                            'soalan' => 'Klorofil terdapat di dalam struktur apakah?',
                            'pilihan' => ['Stoma', 'Xilem', 'Kloroplas', 'Akar'],
                            'jawapan' => 2,
                            'tahap' => 2,
                            'penjelasan' => 'Klorofil terkandung di dalam kloroplas.',
                            'petikan' => 'Kloroplas mengandungi klorofil',
                        ],
                        [
                            'soalan' => 'Sel daun manakah mengandungi kepadatan kloroplas tertinggi?',
                            'pilihan' => ['Sel palisad', 'Sel akar', 'Sel xilem', 'Sel stoma'],
                            'jawapan' => 0,
                            'tahap' => 2,
                            'penjelasan' => 'Kebanyakan kloroplas terletak di dalam sel palisad mesofil.',
                            'petikan' => 'Kebanyakan kloroplas terdapat di dalam sel palisad',
                        ],
                        [
                            'soalan' => 'Mengapakah sel palisad amat penting untuk fotosintesis?',
                            'pilihan' => ['Menyimpan air tumbuhan', 'Mengandungi kebanyakan kloroplas', 'Menyerap air dari tanah', 'Membebaskan karbon dioksida'],
                            'jawapan' => 1,
                            'tahap' => 3,
                            'penjelasan' => 'Kepekatan kloroplas di sel palisad memaksimumkan penyerapan cahaya.',
                            'petikan' => 'Kebanyakan kloroplas terdapat di dalam sel palisad',
                        ],
                    ],
                    'kad' => [
                        ['depan' => 'Kloroplas', 'belakang' => 'Organel tempat fotosintesis berlaku.', 'petikan' => 'Fotosintesis berlaku di dalam kloroplas'],
                        ['depan' => 'Klorofil', 'belakang' => 'Pigmen hijau yang menyerap tenaga cahaya matahari.', 'petikan' => 'pigmen hijau yang menyerap tenaga cahaya'],
                        ['depan' => 'Sel palisad', 'belakang' => 'Sel daun yang padat dengan kloroplas.', 'petikan' => 'Kebanyakan kloroplas terdapat di dalam sel palisad'],
                        ['depan' => 'Xilem', 'belakang' => 'Tisu yang mengangkut air dan garam mineral ke daun.', 'petikan' => 'diangkut ke daun melalui xilem'],
                    ],
                ],
                [
                    'nama' => 'Faktor Kadar Fotosintesis',
                    'ringkasan' => 'Pengaruh keamatan cahaya, suhu, dan kepekatan karbon dioksida',
                    'soalan' => [
                        [
                            'soalan' => 'Antara berikut, faktor manakah yang mempengaruhi kadar fotosintesis?',
                            'pilihan' => ['Warna bunga', 'Saiz akar', 'Keamatan cahaya', 'Bilangan buah'],
                            'jawapan' => 2,
                            'tahap' => 1,
                            'penjelasan' => 'Kadar fotosintesis dipengaruhi oleh keamatan cahaya, karbon dioksida dan suhu.',
                            'petikan' => 'dipengaruhi oleh keamatan cahaya, kepekatan karbon dioksida dan suhu',
                        ],
                        [
                            'soalan' => 'Apakah yang berlaku apabila keamatan cahaya meningkat?',
                            'pilihan' => ['Kadar meningkat sehingga tahap maksimum', 'Kadar sentiasa menurun', 'Kadar serta-merta menjadi sifar', 'Kadar tidak berubah langsung'],
                            'jawapan' => 0,
                            'tahap' => 1,
                            'penjelasan' => 'Kadar meningkat secara berkadaran sehingga mencapai tahap tepu.',
                            'petikan' => 'meningkat apabila keamatan cahaya bertambah sehingga mencapai tahap maksimum',
                        ],
                        [
                            'soalan' => 'Mengapakah suhu yang terlalu tinggi merendahkan kadar fotosintesis?',
                            'pilihan' => ['Cahaya menjadi lemah', 'Enzim boleh dinyahaslikan (rosak)', 'Glukosa bertukar menjadi air', 'Stoma bertukar menjadi klorofil'],
                            'jawapan' => 1,
                            'tahap' => 2,
                            'penjelasan' => 'Suhu ekstrem menyebabkan denaturasi enzim fotosintesis.',
                            'petikan' => 'Suhu yang terlalu tinggi boleh merosakkan enzim',
                        ],
                        [
                            'soalan' => 'Selain cahaya dan suhu, apakah faktor persekitaran ketiga yang mengehadkan fotosintesis?',
                            'pilihan' => ['Kepekatan karbon dioksida', 'Jumlah kanji', 'Warna tanah', 'Panjang daun'],
                            'jawapan' => 0,
                            'tahap' => 2,
                            'penjelasan' => 'Kepekatan karbon dioksida ialah faktor pengehad fotosintesis.',
                            'petikan' => 'kepekatan karbon dioksida dan suhu',
                        ],
                        [
                            'soalan' => 'Sebuah pokok menerima cahaya sangat kuat tetapi kadar fotosintesis tidak meningkat lagi. Mengapa?',
                            'pilihan' => ['Tumbuhan mati', 'Klorofil musnah', 'Kadar telah mencapai tahap maksimum', 'Oksigen telah kehabisan'],
                            'jawapan' => 2,
                            'tahap' => 3,
                            'penjelasan' => 'Kadar mencapai plato maksimum apabila faktor lain menjadi pengehad.',
                            'petikan' => 'sehingga mencapai tahap maksimum',
                        ],
                    ],
                    'kad' => [
                        ['depan' => 'Faktor kadar fotosintesis', 'belakang' => 'Keamatan cahaya, kepekatan karbon dioksida dan suhu.', 'petikan' => 'dipengaruhi oleh keamatan cahaya, kepekatan karbon dioksida dan suhu'],
                        ['depan' => 'Kesan suhu terlalu tinggi', 'belakang' => 'Merosakkan struktur enzim fotosintesis.', 'petikan' => 'Suhu yang terlalu tinggi boleh merosakkan enzim'],
                        ['depan' => 'Kanji', 'belakang' => 'Bentuk simpanan berlebihan glukosa tumbuhan.', 'petikan' => 'Glukosa berlebihan disimpan sebagai kanji'],
                        ['depan' => 'Oksigen', 'belakang' => 'Gas hasil sampingan yang dibebaskan untuk respirasi hidupan.', 'petikan' => 'digunakan oleh hidupan lain untuk bernafas'],
                    ],
                ],
            ],
        ];
    }
}
