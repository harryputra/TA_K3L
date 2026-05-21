<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incident_injuries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incident_report_id')->constrained('incident_reports')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('injury_category_id')->nullable()->constrained('injury_categories')->cascadeOnUpdate()->nullOnDelete();
            $table->foreignId('body_part_id')->nullable()->constrained('body_parts')->cascadeOnUpdate()->nullOnDelete();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        foreach ($this->incidentCategories() as $name => $description) {
            DB::table('incident_categories')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'updated_at' => now(), 'created_at' => now()],
            );
        }

        foreach ($this->injuryCategories() as $name => $description) {
            DB::table('injury_categories')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'updated_at' => now(), 'created_at' => now()],
            );
        }

        foreach ($this->bodyParts() as $name => $description) {
            DB::table('body_parts')->updateOrInsert(
                ['name' => $name],
                ['description' => $description, 'updated_at' => now(), 'created_at' => now()],
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('incident_injuries');
    }

    protected function incidentCategories(): array
    {
        return [
            'Near Miss' => 'Kejadian hampir celaka tanpa cedera atau kerusakan, tetapi berpotensi menjadi insiden.',
            'First Aid Case' => 'Cedera ringan yang cukup ditangani dengan P3K tanpa perawatan medis lanjutan.',
            'Medical Treatment Case' => 'Cedera yang memerlukan pemeriksaan atau tindakan medis profesional.',
            'Lost Time Injury' => 'Cedera yang menyebabkan korban tidak dapat melanjutkan aktivitas/kerja/kuliah pada periode berikutnya.',
            'Fatality' => 'Insiden yang menyebabkan korban meninggal dunia.',
            'Slip, Trip, and Fall' => 'Terpeleset, tersandung, jatuh dari ketinggian, atau jatuh di permukaan datar.',
            'Machine or Equipment Incident' => 'Insiden yang melibatkan mesin, peralatan kerja, alat praktik, atau perkakas.',
            'Electrical Incident' => 'Insiden sengatan listrik, korsleting, percikan listrik, atau panel/instalasi listrik.',
            'Chemical Exposure or Spill' => 'Paparan, tumpahan, percikan, atau kontak dengan bahan kimia.',
            'Fire or Explosion' => 'Kebakaran, ledakan, asap, panas berlebih, atau potensi penyalaan.',
            'Ergonomic or Manual Handling Incident' => 'Cedera karena postur kerja, pengangkatan, dorongan, tarikan, atau gerakan berulang.',
            'Vehicle or Traffic Incident' => 'Insiden kendaraan, forklift, motor, mobil, parkir, atau lalu lintas area kampus.',
            'Environmental Incident' => 'Pencemaran, limbah, tumpahan ke lingkungan, kebisingan, atau gangguan lingkungan.',
            'Property Damage' => 'Kerusakan fasilitas, alat, bangunan, material, atau aset tanpa cedera utama.',
            'Violence or Security Incident' => 'Kekerasan, ancaman, keributan, atau kejadian keamanan yang berdampak pada keselamatan.',
            'Other Incident' => 'Insiden lain yang belum masuk kategori tersedia.',
        ];
    }

    protected function injuryCategories(): array
    {
        return [
            'Tidak Ada Cedera' => 'Tidak ada cedera fisik yang teridentifikasi.',
            'Luka Lecet / Abrasi' => 'Permukaan kulit tergores atau terkelupas ringan.',
            'Luka Sayat / Iris' => 'Luka akibat benda tajam dengan tepi relatif rapi.',
            'Luka Robek' => 'Luka terbuka dengan tepi tidak beraturan.',
            'Luka Tusuk' => 'Luka akibat benda runcing yang menembus jaringan.',
            'Memar / Kontusio' => 'Benturan yang menyebabkan perubahan warna, nyeri, atau bengkak.',
            'Bengkak / Peradangan' => 'Pembengkakan lokal akibat benturan, iritasi, atau reaksi jaringan.',
            'Terkilir / Sprain' => 'Cedera ligamen karena gerakan berlebih atau salah tumpuan.',
            'Strain / Cedera Otot' => 'Cedera otot atau tendon akibat tarikan, beban, atau gerakan berulang.',
            'Dislokasi' => 'Pergeseran sendi dari posisi normal.',
            'Patah Tulang / Fraktur' => 'Dugaan atau konfirmasi retak/patah tulang.',
            'Luka Bakar Termal' => 'Cedera karena panas, api, uap, cairan panas, atau permukaan panas.',
            'Luka Bakar Kimia' => 'Cedera karena kontak dengan bahan kimia korosif/iritan.',
            'Luka Bakar Listrik' => 'Cedera akibat arus listrik atau busur listrik.',
            'Iritasi Mata' => 'Mata merah, perih, berair, atau terganggu karena debu, cahaya, atau bahan kimia.',
            'Benda Asing pada Mata' => 'Partikel, serpihan, atau objek masuk ke mata.',
            'Gangguan Pernapasan / Inhalasi' => 'Sesak, batuk, pusing, atau gejala akibat asap, gas, uap, atau debu.',
            'Keracunan / Paparan Sistemik' => 'Gejala akibat tertelan, terhirup, terserap, atau terpapar zat berbahaya.',
            'Pingsan / Kehilangan Kesadaran' => 'Korban pingsan, lemah berat, atau kehilangan kesadaran sementara.',
            'Cedera Kepala' => 'Benturan kepala, pusing, mual, benjol, atau indikasi gegar otak.',
            'Amputasi' => 'Kehilangan sebagian/seluruh anggota tubuh.',
            'Fatal' => 'Cedera yang berakibat meninggal dunia.',
            'Lainnya' => 'Jenis luka lain yang belum tersedia.',
        ];
    }

    protected function bodyParts(): array
    {
        return [
            'Kepala' => 'Area kepala secara umum.',
            'Wajah' => 'Area wajah, pipi, dagu, dan dahi.',
            'Mata Kiri' => 'Mata kiri.',
            'Mata Kanan' => 'Mata kanan.',
            'Telinga' => 'Telinga kiri atau kanan.',
            'Hidung' => 'Hidung.',
            'Mulut / Gigi' => 'Mulut, bibir, lidah, rahang, atau gigi.',
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
            'Tangan Kiri' => 'Telapak atau punggung tangan kiri.',
            'Tangan Kanan' => 'Telapak atau punggung tangan kanan.',
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
            'Kaki Kiri' => 'Telapak atau punggung kaki kiri.',
            'Kaki Kanan' => 'Telapak atau punggung kaki kanan.',
            'Jari Kaki Kiri' => 'Jari kaki kiri.',
            'Jari Kaki Kanan' => 'Jari kaki kanan.',
            'Saluran Pernapasan' => 'Hidung, tenggorokan, paru, atau sistem pernapasan.',
            'Seluruh Tubuh' => 'Paparan atau dampak pada tubuh secara umum.',
            'Lainnya' => 'Bagian tubuh lain yang belum tersedia.',
        ];
    }
};
