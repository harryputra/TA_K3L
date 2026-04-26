<?php

namespace Database\Seeders;

use App\Models\KnowledgeArticle;
use App\Models\KnowledgeCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            [
                'name' => 'Keselamatan Laboratorium',
                'description' => 'Panduan dasar keselamatan saat beraktivitas di laboratorium dan area praktikum.',
            ],
            [
                'name' => 'APD dan Peralatan',
                'description' => 'Materi pemilihan APD, inspeksi, dan penggunaan alat kerja secara aman.',
            ],
            [
                'name' => 'Tanggap Darurat',
                'description' => 'Prosedur respons cepat, evakuasi, dan tindakan awal saat keadaan darurat terjadi.',
            ],
            [
                'name' => 'Pelaporan Insiden',
                'description' => 'Panduan pelaporan insiden, near miss, dan dokumentasi pendukung yang benar.',
            ],
        ])->mapWithKeys(function (array $category) {
            $record = KnowledgeCategory::query()->updateOrCreate(
                ['slug' => Str::slug($category['name'])],
                $category + ['slug' => Str::slug($category['name'])],
            );

            return [$record->slug => $record];
        });

        $author = User::query()
            ->whereHas('role', fn ($query) => $query->where('code', 'admin'))
            ->first()
            ?? User::query()->first();

        foreach ($this->articles() as $article) {
            $category = $categories->get($article['category_slug']);

            KnowledgeArticle::query()->updateOrCreate(
                ['slug' => $article['slug']],
                [
                    'knowledge_category_id' => $category?->id,
                    'title' => $article['title'],
                    'summary' => $article['summary'],
                    'content' => $article['content'],
                    'reading_time' => $article['reading_time'],
                    'status' => 'published',
                    'created_by' => $author?->id,
                    'approved_by' => $author?->id,
                    'published_at' => now(),
                ],
            );
        }
    }

    protected function articles(): array
    {
        return [
            [
                'category_slug' => 'apd-dan-peralatan',
                'slug' => 'panduan-dasar-apd-area-praktikum',
                'title' => 'Panduan Dasar APD di Area Praktikum',
                'summary' => 'Ringkasan jenis APD, kapan harus digunakan, dan kesalahan yang paling sering terjadi saat praktik.',
                'reading_time' => '5 menit',
                'content' => implode("\n\n", [
                    'Alat pelindung diri wajib digunakan sesuai jenis aktivitas, bukan hanya karena aturan umum laboratorium.',
                    'Sebelum praktikum dimulai, periksa kondisi helm, kacamata, sarung tangan, masker, dan sepatu keselamatan. APD yang rusak harus diganti sebelum pekerjaan dilakukan.',
                    'Pilih APD berdasarkan bahaya dominan. Kacamata pelindung cocok untuk serpihan dan percikan ringan, face shield digunakan untuk risiko lontaran lebih tinggi, sedangkan sarung tangan harus dipilih sesuai jenis bahan yang ditangani.',
                    'Kesalahan yang paling sering terjadi adalah menggunakan APD tidak lengkap, melepas APD saat pekerjaan belum selesai, atau menggunakan sarung tangan yang justru berbahaya di sekitar mesin berputar.',
                    'Budaya kerja aman dimulai dari disiplin menggunakan APD secara konsisten sejak persiapan sampai area dinyatakan aman.',
                ]),
            ],
            [
                'category_slug' => 'tanggap-darurat',
                'slug' => 'tanggap-awal-saat-tumpahan-bahan-kimia',
                'title' => 'Tanggap Awal Saat Terjadi Tumpahan Bahan Kimia',
                'summary' => 'Langkah aman untuk mengisolasi area, melindungi diri, dan meminta bantuan secara cepat.',
                'reading_time' => '7 menit',
                'content' => implode("\n\n", [
                    'Jangan langsung membersihkan tumpahan sebelum mengenali jenis bahan yang terlibat dan tingkat bahayanya.',
                    'Amankan diri dengan APD yang sesuai, batasi akses ke area tumpahan, dan beri tahu orang di sekitar agar menjauh dari sumber paparan.',
                    'Jika bahan mengenai kulit atau mata, lakukan pembilasan dengan air mengalir selama minimal 15 menit dan hubungi petugas kampus atau tenaga medis.',
                    'Gunakan spill kit hanya bila Anda memahami prosedurnya. Untuk bahan yang mudah menguap, mudah terbakar, atau belum dikenali, prioritaskan isolasi area dan permintaan bantuan.',
                    'Setelah situasi terkendali, dokumentasikan waktu kejadian, lokasi, bahan yang tumpah, dan tindakan awal yang sudah dilakukan sebagai bahan pelaporan insiden.',
                ]),
            ],
            [
                'category_slug' => 'keselamatan-laboratorium',
                'slug' => 'checklist-keselamatan-sebelum-praktikum',
                'title' => 'Checklist Keselamatan Sebelum Memulai Praktikum',
                'summary' => 'Daftar pemeriksaan sederhana agar aktivitas berjalan aman sejak awal.',
                'reading_time' => '4 menit',
                'content' => implode("\n\n", [
                    'Mulai dengan memastikan area kerja rapi, jalur evakuasi tidak terhalang, dan peralatan darurat dapat diakses.',
                    'Periksa kembali instruksi kerja, SOP, dan bahan atau alat yang akan digunakan. Jangan memulai bila ada langkah yang belum dipahami.',
                    'Pastikan kondisi alat layak pakai, kabel tidak rusak, pengaman mesin terpasang, dan sumber energi dapat dikendalikan.',
                    'Lakukan briefing singkat dengan anggota tim agar semua orang memahami pembagian tugas dan potensi bahaya utama di area tersebut.',
                    'Checklist singkat sebelum praktik sering kali menjadi pembeda antara pekerjaan yang aman dan insiden yang sebenarnya bisa dicegah.',
                ]),
            ],
            [
                'category_slug' => 'pelaporan-insiden',
                'slug' => 'cara-membuat-laporan-insiden-yang-jelas',
                'title' => 'Cara Membuat Laporan Insiden yang Jelas dan Lengkap',
                'summary' => 'Panduan singkat menulis kronologi, tindakan awal, dan bukti pendukung dengan baik.',
                'reading_time' => '6 menit',
                'content' => implode("\n\n", [
                    'Laporan yang baik harus menjawab apa yang terjadi, kapan terjadi, di mana lokasi kejadian, siapa yang terlibat, dan tindakan awal apa yang sudah dilakukan.',
                    'Tuliskan kronologi secara runtut dan faktual. Hindari asumsi yang belum bisa dibuktikan dan gunakan bahasa yang mudah dipahami.',
                    'Lampirkan bukti pendukung seperti foto area, kondisi alat, atau dampak yang terlihat agar proses verifikasi lebih cepat.',
                    'Jika ada korban atau potensi korban, jelaskan kondisi awal dan bantuan yang telah diberikan saat itu.',
                    'Pelaporan yang jelas bukan untuk mencari kesalahan, melainkan untuk memastikan tindak lanjut dan pencegahan kejadian serupa.',
                ]),
            ],
        ];
    }
}
