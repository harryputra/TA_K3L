<?php

namespace App\Support\Dashboard;

use App\Models\IncidentReport;
use App\Models\KnowledgeArticle;
use App\Models\PotentialHazardReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class UserDashboardData
{
    public function build(int $userId): array
    {
        $reportsQuery = IncidentReport::query()->where('reported_by', $userId);
        $recentReports = IncidentReport::query()
            ->with(['category', 'location'])
            ->where('reported_by', $userId)
            ->latest()
            ->take(5)
            ->get();
        $recentHazardReports = collect();

        $latestReport = $recentReports->first();
        $latestHazard = null;
        $featuredKnowledge = null;
        $knowledgeRecommendations = collect();
        $publishedKnowledgeCount = 0;

        if (class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')) {
            $hazardQuery = PotentialHazardReport::query()
                ->with(['location', 'reviewer', 'resolver'])
                ->where('reported_by', $userId);

            $recentHazardReports = (clone $hazardQuery)
                ->latest('submitted_at')
                ->take(5)
                ->get();

            $latestHazard = $recentHazardReports->first();
        }

        if (class_exists(KnowledgeArticle::class) && Schema::hasTable('knowledge_articles')) {
            $knowledgeQuery = KnowledgeArticle::query()
                ->with('category')
                ->where('status', 'published');

            $publishedKnowledgeCount = (clone $knowledgeQuery)->count();
            $featuredKnowledge = (clone $knowledgeQuery)->latest('published_at')->first();
            $knowledgeRecommendations = (clone $knowledgeQuery)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return [
            'stats' => [
                'my_reports' => (clone $reportsQuery)->count(),
                'submitted_reports' => (clone $reportsQuery)->where('status', 'submitted')->count(),
                'verified_reports' => (clone $reportsQuery)->where('status', 'verified')->count(),
                'closed_reports' => (clone $reportsQuery)->where('status', 'closed')->count(),
                'my_hazards' => class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
                    ? PotentialHazardReport::query()->where('reported_by', $userId)->count()
                    : 0,
                'resolved_hazards' => class_exists(PotentialHazardReport::class) && Schema::hasTable('potential_hazard_reports')
                    ? PotentialHazardReport::query()->where('reported_by', $userId)->where('status', 'resolved')->count()
                    : 0,
            ],
            'recentReports' => $recentReports,
            'recentHazardReports' => $recentHazardReports,
            'publishedKnowledgeCount' => $publishedKnowledgeCount,
            'latestReportSummary' => $this->buildLatestReportSummary($latestReport),
            'latestHazardSummary' => $this->buildLatestHazardSummary($latestHazard),
            'featuredKnowledge' => $featuredKnowledge,
            'knowledgeRecommendations' => $knowledgeRecommendations,
        ];
    }

    protected function buildLatestReportSummary(?IncidentReport $report): array
    {
        $steps = [
            ['number' => 1, 'label' => 'Masuk', 'active' => false],
            ['number' => 2, 'label' => 'Validasi', 'active' => false],
            ['number' => 3, 'label' => 'Investigasi', 'active' => false],
            ['number' => 4, 'label' => 'Tindakan', 'active' => false],
            ['number' => 5, 'label' => 'Selesai', 'active' => false],
        ];

        if (! $report) {
            return [
                'report' => null,
                'steps' => collect($steps),
                'progress_percent' => 0,
                'status_label' => 'Belum ada laporan',
                'status_note' => 'Buat laporan pertama Anda untuk melihat progres pelaporan secara real-time.',
            ];
        }

        $level = match ($report->status) {
            'submitted' => 1,
            'verified' => 2,
            'investigating' => 3,
            'resolved' => 4,
            'closed' => 5,
            'rejected' => 2,
            default => 0,
        };

        $statusLabel = match ($report->status) {
            'submitted' => 'Menunggu validasi',
            'verified' => 'Terverifikasi',
            'investigating' => 'Sedang investigasi',
            'resolved' => 'Tindakan selesai',
            'closed' => 'Laporan ditutup',
            'rejected' => 'Perlu perbaikan data',
            default => ucfirst($report->status),
        };

        $statusNote = match ($report->status) {
            'submitted' => 'Satgas sudah menerima laporan Anda dan akan melakukan pemeriksaan awal.',
            'verified' => 'Laporan sudah lolos validasi awal dan menunggu tindak lanjut berikutnya.',
            'investigating' => 'Tim sedang melakukan penelusuran dan pengumpulan informasi lapangan.',
            'resolved' => 'Tindakan perbaikan utama sudah dilakukan dan menunggu penutupan resmi.',
            'closed' => 'Kasus sudah dinyatakan selesai dan ditutup secara resmi.',
            'rejected' => 'Laporan memerlukan klarifikasi atau data tambahan sebelum diproses lebih lanjut.',
            default => 'Status laporan akan diperbarui secara berkala oleh Satgas.',
        };

        return [
            'report' => $report,
            'steps' => collect($steps)->map(fn (array $step) => [
                ...$step,
                'active' => $level >= $step['number'],
            ]),
            'progress_percent' => $level > 0 ? (int) round(($level / count($steps)) * 100) : 0,
            'status_label' => $statusLabel,
            'status_note' => $statusNote,
        ];
    }

    protected function buildLatestHazardSummary(?PotentialHazardReport $report): array
    {
        if (! $report) {
            return [
                'report' => null,
                'status_label' => 'Belum ada hazard report',
                'status_note' => 'Kirim temuan potensi bahaya untuk memantau progres penanganannya di sini.',
                'handled_by' => '-',
            ];
        }

        $statusLabel = match ($report->status) {
            'submitted' => 'Menunggu review satgas',
            'reviewed' => 'Sedang ditindaklanjuti',
            'resolved' => 'Hazard selesai ditangani',
            default => ucfirst($report->status),
        };

        $statusNote = match ($report->status) {
            'submitted' => 'Temuan Anda sudah masuk ke sistem dan menunggu pemeriksaan awal dari Satgas.',
            'reviewed' => $report->response_note ?: 'Satgas sudah meninjau hazard dan sedang menyiapkan atau menjalankan tindakan.',
            'resolved' => $report->response_note ?: 'Hazard sudah dinyatakan selesai ditangani oleh Satgas.',
            default => $report->response_note ?: 'Status hazard akan diperbarui secara berkala.',
        };

        return [
            'report' => $report,
            'status_label' => $statusLabel,
            'status_note' => $statusNote,
            'handled_by' => $report->resolver?->name ?? $report->reviewer?->name ?? '-',
        ];
    }
}
