<?php

namespace Database\Seeders;

use App\Models\BodyPart;
use App\Models\IncidentCategory;
use App\Models\InjuryCategory;
use App\Models\Location;
use Illuminate\Database\Seeder;

class IncidentReferenceSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->incidentCategories() as $name => $description) {
            IncidentCategory::query()->updateOrCreate(['name' => $name], ['description' => $description]);
        }

        foreach ($this->injuryCategories() as $name => $description) {
            InjuryCategory::query()->updateOrCreate(['name' => $name], ['description' => $description]);
        }

        foreach ($this->bodyParts() as $name => $description) {
            BodyPart::query()->updateOrCreate(['name' => $name], ['description' => $description]);
        }

        foreach ([
            ['name' => 'Laboratorium Kimia', 'code' => 'LAB-KIM', 'description' => 'Area laboratorium praktikum kimia.'],
            ['name' => 'Workshop Teknik', 'code' => 'WORKSHOP', 'description' => 'Area kerja praktik dan peralatan teknik.'],
            ['name' => 'Gedung Perkuliahan A', 'code' => 'GPA', 'description' => 'Gedung kelas dan koridor akademik.'],
        ] as $location) {
            Location::query()->updateOrCreate(
                ['code' => $location['code']],
                [...$location, 'is_active' => true],
            );
        }
    }

    protected function incidentCategories(): array
    {
        return [
            'Near Miss' => 'Kejadian hampir celaka tanpa cedera atau kerusakan.',
            'First Aid Case' => 'Cedera ringan yang cukup ditangani dengan P3K.',
            'Medical Treatment Case' => 'Cedera yang memerlukan tindakan medis profesional.',
            'Lost Time Injury' => 'Cedera yang menyebabkan korban tidak dapat melanjutkan aktivitas berikutnya.',
            'Fatality' => 'Insiden yang menyebabkan korban meninggal dunia.',
            'Slip, Trip, and Fall' => 'Terpeleset, tersandung, atau jatuh.',
            'Machine or Equipment Incident' => 'Insiden yang melibatkan mesin, peralatan kerja, atau perkakas.',
            'Electrical Incident' => 'Insiden sengatan, korsleting, percikan, atau instalasi listrik.',
            'Chemical Exposure or Spill' => 'Paparan, tumpahan, percikan, atau kontak bahan kimia.',
            'Fire or Explosion' => 'Kebakaran, ledakan, asap, panas berlebih, atau potensi penyalaan.',
            'Ergonomic or Manual Handling Incident' => 'Cedera karena postur, pengangkatan, atau gerakan berulang.',
            'Vehicle or Traffic Incident' => 'Insiden kendaraan atau lalu lintas area kampus.',
            'Environmental Incident' => 'Pencemaran, limbah, tumpahan ke lingkungan, atau gangguan lingkungan.',
            'Property Damage' => 'Kerusakan fasilitas, alat, bangunan, material, atau aset.',
            'Violence or Security Incident' => 'Kekerasan, ancaman, keributan, atau kejadian keamanan.',
            'Other Incident' => 'Insiden lain yang belum masuk kategori tersedia.',
        ];
    }

    protected function injuryCategories(): array
    {
        return [
            'Tidak Ada Cedera' => 'Tidak ada cedera fisik yang teridentifikasi.',
            'Luka Lecet / Abrasi' => 'Permukaan kulit tergores atau terkelupas ringan.',
            'Luka Sayat / Iris' => 'Luka akibat benda tajam.',
            'Luka Robek' => 'Luka terbuka dengan tepi tidak beraturan.',
            'Luka Tusuk' => 'Luka akibat benda runcing yang menembus jaringan.',
            'Memar / Kontusio' => 'Benturan yang menyebabkan nyeri, bengkak, atau perubahan warna.',
            'Bengkak / Peradangan' => 'Pembengkakan lokal.',
            'Terkilir / Sprain' => 'Cedera ligamen karena salah tumpuan.',
            'Strain / Cedera Otot' => 'Cedera otot atau tendon.',
            'Dislokasi' => 'Pergeseran sendi dari posisi normal.',
            'Patah Tulang / Fraktur' => 'Dugaan atau konfirmasi retak/patah tulang.',
            'Luka Bakar Termal' => 'Cedera karena panas, api, uap, atau cairan panas.',
            'Luka Bakar Kimia' => 'Cedera karena kontak bahan kimia.',
            'Luka Bakar Listrik' => 'Cedera akibat arus listrik atau busur listrik.',
            'Iritasi Mata' => 'Mata merah, perih, berair, atau terganggu.',
            'Benda Asing pada Mata' => 'Partikel atau objek masuk ke mata.',
            'Gangguan Pernapasan / Inhalasi' => 'Gejala akibat asap, gas, uap, atau debu.',
            'Keracunan / Paparan Sistemik' => 'Gejala akibat paparan zat berbahaya.',
            'Pingsan / Kehilangan Kesadaran' => 'Korban pingsan atau kehilangan kesadaran sementara.',
            'Cedera Kepala' => 'Benturan kepala atau indikasi gegar otak.',
            'Amputasi' => 'Kehilangan sebagian/seluruh anggota tubuh.',
            'Fatal' => 'Cedera yang berakibat meninggal dunia.',
            'Lainnya' => 'Jenis luka lain yang belum tersedia.',
        ];
    }

    protected function bodyParts(): array
    {
        return [
            'Kepala' => 'Area kepala secara umum.',
            'Wajah' => 'Area wajah.',
            'Mata Kiri' => 'Mata kiri.',
            'Mata Kanan' => 'Mata kanan.',
            'Telinga' => 'Telinga kiri atau kanan.',
            'Hidung' => 'Hidung.',
            'Mulut / Gigi' => 'Mulut, bibir, rahang, atau gigi.',
            'Leher' => 'Area leher.',
            'Bahu Kiri' => 'Bahu kiri.',
            'Bahu Kanan' => 'Bahu kanan.',
            'Lengan Atas Kiri' => 'Lengan atas kiri.',
            'Lengan Atas Kanan' => 'Lengan atas kanan.',
            'Siku Kiri' => 'Siku kiri.',
            'Siku Kanan' => 'Siku kanan.',
            'Lengan Bawah Kiri' => 'Lengan bawah kiri.',
            'Lengan Bawah Kanan' => 'Lengan bawah kanan.',
            'Pergelangan Tangan Kiri' => 'Pergelangan tangan kiri.',
            'Pergelangan Tangan Kanan' => 'Pergelangan tangan kanan.',
            'Tangan Kiri' => 'Tangan kiri.',
            'Tangan Kanan' => 'Tangan kanan.',
            'Jari Tangan Kiri' => 'Jari tangan kiri.',
            'Jari Tangan Kanan' => 'Jari tangan kanan.',
            'Dada' => 'Area dada.',
            'Punggung' => 'Area punggung.',
            'Perut' => 'Area perut.',
            'Pinggang' => 'Area pinggang.',
            'Panggul' => 'Area panggul.',
            'Paha Kiri' => 'Paha kiri.',
            'Paha Kanan' => 'Paha kanan.',
            'Lutut Kiri' => 'Lutut kiri.',
            'Lutut Kanan' => 'Lutut kanan.',
            'Betis Kiri' => 'Betis kiri.',
            'Betis Kanan' => 'Betis kanan.',
            'Pergelangan Kaki Kiri' => 'Pergelangan kaki kiri.',
            'Pergelangan Kaki Kanan' => 'Pergelangan kaki kanan.',
            'Kaki Kiri' => 'Kaki kiri.',
            'Kaki Kanan' => 'Kaki kanan.',
            'Jari Kaki Kiri' => 'Jari kaki kiri.',
            'Jari Kaki Kanan' => 'Jari kaki kanan.',
            'Saluran Pernapasan' => 'Hidung, tenggorokan, paru, atau sistem pernapasan.',
            'Seluruh Tubuh' => 'Paparan atau dampak tubuh secara umum.',
            'Lainnya' => 'Bagian tubuh lain yang belum tersedia.',
        ];
    }
}
