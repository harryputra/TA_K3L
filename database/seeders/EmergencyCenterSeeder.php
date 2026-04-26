<?php

namespace Database\Seeders;

use App\Models\EmergencyContact;
use App\Models\EmergencyResponseStep;
use App\Models\FirstAidAction;
use App\Models\FirstAidGuide;
use Illuminate\Database\Seeder;

class EmergencyCenterSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->contacts() as $index => $contact) {
            EmergencyContact::query()->updateOrCreate(
                ['name' => $contact['name']],
                $contact + ['sort_order' => $index + 1, 'is_active' => true],
            );
        }

        foreach ($this->responseSteps() as $index => $step) {
            EmergencyResponseStep::query()->updateOrCreate(
                ['title' => $step['title']],
                $step + ['sort_order' => $index + 1, 'is_active' => true],
            );
        }

        foreach ($this->guides() as $index => $guideData) {
            $guide = FirstAidGuide::query()->updateOrCreate(
                ['title' => $guideData['title']],
                [
                    'icon' => $guideData['icon'],
                    'accent_class' => $guideData['accent_class'],
                    'summary' => $guideData['summary'],
                    'sort_order' => $index + 1,
                    'is_active' => true,
                ],
            );

            foreach ($guideData['actions'] as $actionIndex => $action) {
                FirstAidAction::query()->updateOrCreate(
                    [
                        'first_aid_guide_id' => $guide->id,
                        'sort_order' => $actionIndex + 1,
                    ],
                    ['description' => $action],
                );
            }
        }
    }

    protected function contacts(): array
    {
        return [
            [
                'name' => 'Satgas K3L Kampus',
                'phone_number' => '0411-555-0101',
                'description' => 'Koordinasi awal kejadian dan pengamanan area.',
                'icon' => 'shield_person',
                'color_class' => 'bg-[var(--primary-color)]',
            ],
            [
                'name' => 'Klinik / UKS Kampus',
                'phone_number' => '0411-555-0102',
                'description' => 'Pertolongan pertama dan rujukan medis awal.',
                'icon' => 'medical_services',
                'color_class' => 'bg-[var(--green)]',
            ],
            [
                'name' => 'Security Kampus',
                'phone_number' => '0411-555-0103',
                'description' => 'Kontrol akses area dan pengamanan lokasi insiden.',
                'icon' => 'local_police',
                'color_class' => 'bg-[var(--orange)]',
            ],
            [
                'name' => 'Pemadam / Ambulans',
                'phone_number' => '112',
                'description' => 'Untuk kebakaran, evakuasi, dan kondisi kritis.',
                'icon' => 'emergency',
                'color_class' => 'bg-[var(--red)]',
            ],
        ];
    }

    protected function responseSteps(): array
    {
        return [
            ['title' => 'Amankan diri', 'description' => 'Pastikan Anda dan orang di sekitar berada di posisi aman sebelum melakukan tindakan lain.'],
            ['title' => 'Hubungi bantuan', 'description' => 'Pilih kontak yang paling sesuai dengan jenis kejadian untuk percepatan respons.'],
            ['title' => 'Dokumentasikan kondisi', 'description' => 'Ambil bukti foto atau catatan singkat jika situasi sudah aman untuk didokumentasikan.'],
            ['title' => 'Kirim laporan', 'description' => 'Isi form pelaporan agar Satgas dapat melakukan verifikasi dan tindak lanjut.'],
        ];
    }

    protected function guides(): array
    {
        return [
            [
                'title' => 'Luka Sayat / Pendarahan',
                'icon' => 'bloodtype',
                'accent_class' => 'bg-rose-500',
                'summary' => 'Fokus utama adalah menghentikan perdarahan dan menjaga luka tetap bersih sampai bantuan datang.',
                'actions' => [
                    'Tekan area luka dengan kain bersih atau kasa steril selama beberapa menit.',
                    'Angkat bagian tubuh yang terluka lebih tinggi jika memungkinkan.',
                    'Jika darah menembus balutan, tambahkan lapisan baru tanpa melepas lapisan pertama.',
                    'Segera ke klinik kampus jika perdarahan tidak berhenti atau luka cukup dalam.',
                ],
            ],
            [
                'title' => 'Luka Bakar Ringan',
                'icon' => 'local_fire_department',
                'accent_class' => 'bg-orange-500',
                'summary' => 'Dinginkan area yang terbakar secepat mungkin dan hindari tindakan yang memperparah jaringan kulit.',
                'actions' => [
                    'Aliri area terbakar dengan air mengalir suhu normal selama 10-20 menit.',
                    'Lepaskan aksesori di sekitar area luka jika tidak menempel pada kulit.',
                    'Jangan oleskan pasta gigi, minyak, atau es batu langsung ke luka.',
                    'Tutup longgar dengan kain bersih dan rujuk ke klinik bila area luas atau melepuh.',
                ],
            ],
            [
                'title' => 'Pingsan / Tidak Sadar',
                'icon' => 'airway',
                'accent_class' => 'bg-amber-500',
                'summary' => 'Utamakan pengecekan respons, napas, dan posisi tubuh agar aliran udara tetap aman.',
                'actions' => [
                    'Panggil korban dan cek respons secara hati-hati.',
                    'Jika bernapas, baringkan miring stabil dan longgarkan pakaian yang ketat.',
                    'Jangan memberi makan atau minum saat korban belum sadar penuh.',
                    'Hubungi bantuan medis segera jika korban tidak sadar lebih dari 1 menit atau ada cedera kepala.',
                ],
            ],
            [
                'title' => 'Paparan Listrik',
                'icon' => 'bolt',
                'accent_class' => 'bg-blue-600',
                'summary' => 'Jangan sentuh korban langsung sebelum sumber listrik dipastikan aman atau diputus.',
                'actions' => [
                    'Matikan sumber listrik dari panel atau gunakan benda non-konduktor untuk menjauhkan sumber.',
                    'Jangan menyentuh korban dengan tangan kosong selama arus belum dipastikan terputus.',
                    'Setelah aman, cek napas dan respons korban.',
                    'Segera hubungi bantuan medis karena cedera listrik bisa berbahaya meski luka luar tampak kecil.',
                ],
            ],
            [
                'title' => 'Paparan Bahan Kimia',
                'icon' => 'science',
                'accent_class' => 'bg-emerald-600',
                'summary' => 'Pisahkan korban dari sumber paparan dan lakukan pembilasan cepat sesuai area yang terkena.',
                'actions' => [
                    'Jauhkan korban dari sumber paparan dan gunakan APD bila membantu korban.',
                    'Bilas kulit atau mata dengan air mengalir selama minimal 15 menit.',
                    'Lepaskan pakaian yang terkontaminasi secara hati-hati.',
                    'Simpan informasi bahan kimia yang terlibat untuk disampaikan ke petugas medis.',
                ],
            ],
            [
                'title' => 'Tersedak Ringan / Berat',
                'icon' => 'masks',
                'accent_class' => 'bg-violet-600',
                'summary' => 'Bedakan apakah korban masih bisa batuk atau bicara, karena langkah bantuannya berbeda.',
                'actions' => [
                    'Jika korban masih bisa batuk atau bicara, minta terus batuk dan pantau kondisinya.',
                    'Jika korban tidak bisa bicara atau bernapas, lakukan bantuan tersedak sesuai pelatihan yang dimiliki.',
                    'Segera panggil bantuan orang sekitar dan kontak darurat kampus.',
                    'Setelah benda keluar, korban tetap perlu diperiksa bila sempat sesak berat.',
                ],
            ],
        ];
    }
}
