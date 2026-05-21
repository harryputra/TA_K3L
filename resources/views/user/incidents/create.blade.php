@extends('user.layouts.app')

@section('title', 'Form Pelaporan Insiden')

@section('page')
    <header id="header" class="relative flex h-135 w-full flex-col items-center justify-center gap-4 px-6">
        <div class="pointer-events-none absolute inset-x-0 bottom-8 mx-auto h-28 w-[82%] rounded-full bg-white/12 blur-3xl"></div>
        <div class="relative z-1 flex max-w-6xl flex-col items-center">
            <span class="inline-flex rounded-full border border-white/20 bg-white/12 px-5 py-2 text-xs font-semibold uppercase tracking-[0.35em] text-white/90">
                Portal Operasional K3L
            </span>
            <h1 class="mt-6 text-center text-5xl font-bold text-white lg:text-7xl">Pelaporan Insiden</h1>
            <p class="max-w-6xl px-4 pt-2 text-center text-lg text-white/90 lg:text-2xl">
                Laporkan kejadian kecelakaan kerja, cedera, atau insiden operasional agar tim terkait dapat segera
                melakukan penanganan dan tindak lanjut.
            </p>
        </div>
    </header>

    <main class="w-full bg-[#f6f8fc] pb-14">
        @include('reports.partials.incident-form', [
            'showInlineFlash' => true,
            'isPublicIncidentForm' => true,
            'formAction' => route('user.incidents.store'),
            'submitLabel' => 'Kirim Laporan Insiden',
            'cancelUrl' => route('user.dashboard'),
            'panelEyebrow' => 'Form Pelaporan',
            'panelTitle' => 'Laporkan insiden dengan cepat',
            'panelDescription' => 'Isi data kejadian, lokasi GPS, korban, dampak awal, kronologi, dan bukti pendukung. Satgas akan melengkapi verifikasi dan tindak lanjut.',
            'summaryTips' => [
                ['label' => 'Pelapor', 'value' => 'Isi kontak aktif agar pembaruan status dapat dikirim.'],
                ['label' => 'Lampiran', 'value' => 'Maksimal 3 file untuk memudahkan verifikasi awal.'],
                ['label' => 'Status Awal', 'value' => 'Laporan akan masuk ke antrean review Satgas.'],
            ],
            'sidebarEyebrow' => 'Panduan Singkat',
            'sidebarTitle' => 'Agar laporan lebih cepat diproses',
            'sidebarDescription' => 'Lengkapi informasi yang pelapor ketahui di lapangan. Bagian verifikasi, keputusan status, PIC, dan tindak lanjut tetap dilengkapi Satgas.',
            'sidebarSteps' => [
                ['title' => 'Izinkan akses GPS', 'description' => 'Lokasi utama akan otomatis mengikuti area gedung yang terdeteksi.'],
                ['title' => 'Isi dampak dan korban', 'description' => 'Tambahkan data korban, luka, APD, dan dampak awal bila informasinya tersedia.'],
                ['title' => 'Jelaskan urutan kejadian', 'description' => 'Tuliskan kronologi singkat tapi runtut, mulai dari awal sampai kondisi terakhir.'],
                ['title' => 'Lampirkan bukti yang relevan', 'description' => 'Foto alat, area, atau kondisi terkini akan sangat membantu proses review.'],
            ],
            'emergencyTitle' => 'Butuh respons darurat?',
            'emergencyDescription' => 'Jika kejadian ini melibatkan cedera langsung, kebakaran, atau risiko lanjutan yang aktif, segera gunakan pusat darurat agar bantuan awal tidak tertunda.',
        ])
    </main>
@endsection
