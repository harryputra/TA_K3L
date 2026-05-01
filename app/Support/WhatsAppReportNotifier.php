<?php

namespace App\Support;

use App\Jobs\SendWhatsAppNotification;
use App\Models\IncidentReport;
use App\Models\PotentialHazardReport;
use App\Models\User;

class WhatsAppReportNotifier
{
    public function incidentCreated(IncidentReport $report): void
    {
        $this->dispatch($report->reporter_whatsapp, implode("\n", [
            "Halo {$this->name($report->reporter_name)}, laporan insiden Anda berhasil dikirim.",
            "Nomor laporan: {$report->report_number}",
            'Status: Menunggu review Satgas',
            'Cek status: ' . route('user.incidents.status', ['q' => $report->report_number]),
        ]));
    }

    public function satgasIncidentCreated(IncidentReport $report): void
    {
        $report->loadMissing(['location', 'reporter']);

        $message = implode("\n", [
            'Notifikasi SIAGA POLMAN',
            'Ada laporan insiden baru yang perlu direview Satgas.',
            "Nomor: {$report->report_number}",
            "Judul: {$report->title}",
            'Pelapor: ' . ($report->reporter?->name ?? $report->reporter_name ?? '-'),
            'Kontak: ' . ($report->reporter_whatsapp ?? '-'),
            'Lokasi: ' . ($report->location?->name ?? '-'),
            'Tanggal kejadian: ' . ($report->incident_date?->format('d M Y') ?? '-'),
            'Buka detail: ' . route('satgas.incidents.show', $report),
        ]);

        $this->dispatchToSatgas($message);
    }

    public function incidentStatusUpdated(IncidentReport $report, string $status, ?string $note = null): void
    {
        $lines = [
            "Halo {$this->name($report->reporter_name)}, status laporan {$report->report_number} diperbarui.",
            'Status: ' . $this->incidentStatusLabel($status),
        ];

        if (filled($note)) {
            $lines[] = 'Catatan: ' . $note;
        }

        $lines[] = 'Cek status: ' . route('user.incidents.status', ['q' => $report->report_number]);

        $this->dispatch($report->reporter_whatsapp, implode("\n", $lines));
    }

    public function hazardCreated(PotentialHazardReport $report): void
    {
        $this->dispatch($report->reporter_whatsapp, implode("\n", [
            "Halo {$this->name($report->reporter_name)}, laporan potensi bahaya Anda berhasil dikirim.",
            "Nomor laporan: {$report->report_number}",
            'Status: Menunggu review Satgas',
            'Pembaruan berikutnya akan dikirim melalui WhatsApp ini.',
        ]));
    }

    public function satgasHazardCreated(PotentialHazardReport $report): void
    {
        $report->loadMissing(['location', 'reporter']);

        $message = implode("\n", [
            'Notifikasi SIAGA POLMAN',
            'Ada laporan potensi bahaya baru yang perlu direview Satgas.',
            "Nomor: {$report->report_number}",
            "Judul: {$report->title}",
            'Jenis: ' . str_replace('-', ' ', $report->hazard_type),
            'Pelapor: ' . ($report->reporter?->name ?? $report->reporter_name ?? '-'),
            'Kontak: ' . ($report->reporter_whatsapp ?? '-'),
            'Lokasi: ' . ($report->location?->name ?? '-'),
            'Titik spesifik: ' . ($report->specific_location ?? '-'),
            'Buka detail: ' . route('satgas.hazards.show', $report),
        ]);

        $this->dispatchToSatgas($message);
    }

    public function hazardStatusUpdated(PotentialHazardReport $report, string $status, ?string $note = null): void
    {
        $lines = [
            "Halo {$this->name($report->reporter_name)}, status laporan potensi bahaya {$report->report_number} diperbarui.",
            'Status: ' . $this->hazardStatusLabel($status),
        ];

        if (filled($note)) {
            $lines[] = 'Catatan: ' . $note;
        }

        $this->dispatch($report->reporter_whatsapp, implode("\n", $lines));
    }

    protected function dispatch(?string $target, string $message): void
    {
        if (blank($target)) {
            return;
        }

        SendWhatsAppNotification::dispatch($target, $message)->afterCommit();
    }

    protected function dispatchToSatgas(string $message): void
    {
        User::query()
            ->where('is_active', true)
            ->whereNotNull('phone')
            ->whereHas('role', fn ($query) => $query->where('code', 'satgas'))
            ->pluck('phone')
            ->filter()
            ->unique()
            ->each(fn (string $phone) => $this->dispatch($phone, $message));
    }

    protected function name(?string $name): string
    {
        return filled($name) ? $name : 'Pelapor';
    }

    protected function incidentStatusLabel(string $status): string
    {
        return match ($status) {
            'submitted' => 'Menunggu review Satgas',
            'verified' => 'Terverifikasi',
            'investigating' => 'Sedang ditindaklanjuti',
            'resolved' => 'Tindakan selesai',
            'closed' => 'Selesai',
            'rejected' => 'Ditolak / perlu klarifikasi',
            default => ucfirst($status),
        };
    }

    protected function hazardStatusLabel(string $status): string
    {
        return match ($status) {
            'submitted' => 'Menunggu review Satgas',
            'reviewed' => 'Sedang ditinjau / ditindaklanjuti',
            'resolved' => 'Selesai ditangani',
            default => ucfirst($status),
        };
    }
}
