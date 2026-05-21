<?php

namespace App\Actions\Incidents;

use App\Models\IncidentAttachment;
use App\Models\IncidentInjury;
use App\Models\IncidentReport;
use App\Support\ActivityLogger;
use App\Support\WhatsAppReportNotifier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateIncidentReport
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected WhatsAppReportNotifier $whatsAppReportNotifier,
    ) {
    }

    public function handle(array $validated, ?int $reporterId = null): IncidentReport
    {
        return DB::transaction(function () use ($validated, $reporterId) {
            $attachments = $validated['attachments'] ?? [];
            $injuries = $validated['injuries'] ?? [];
            unset($validated['attachments']);
            unset($validated['injuries']);

            $report = IncidentReport::query()->create([
                ...$validated,
                'report_number' => $this->generateReportNumber(),
                'reported_by' => $reporterId,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($attachments as $attachment) {
                $this->storeAttachment($report, $attachment, $reporterId);
            }

            foreach ($injuries as $injury) {
                $this->storeInjury($report, $injury);
            }

            $report->statusHistories()->create([
                'from_status' => null,
                'to_status' => 'submitted',
                'changed_by' => $reporterId,
                'change_note' => 'Laporan insiden dibuat oleh pelapor.',
                'created_at' => now(),
            ]);

            if ($reporterId !== null) {
                $this->activityLogger->log(
                    $reporterId,
                    $reporterId,
                    'incident_created',
                    'Laporan insiden berhasil dikirim',
                    "Laporan {$report->report_number} dengan judul \"{$report->title}\" sedang menunggu review Satgas.",
                    $report,
                    [
                        'status' => 'submitted',
                        'report_number' => $report->report_number,
                    ],
                );
            }

            $this->whatsAppReportNotifier->incidentCreated($report);
            $this->whatsAppReportNotifier->satgasIncidentCreated($report);

            return $report->load(['category', 'location', 'attachments', 'injuries.injuryCategory', 'injuries.bodyPart']);
        });
    }

    protected function generateReportNumber(): string
    {
        return 'INC-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
    }

    protected function storeAttachment(IncidentReport $report, UploadedFile $attachment, ?int $reporterId): void
    {
        $path = $attachment->store('incident-attachments', 'public');

        IncidentAttachment::query()->create([
            'incident_report_id' => $report->id,
            'file_name' => $attachment->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $attachment->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $attachment->getSize(),
            'uploaded_by' => $reporterId,
        ]);
    }

    protected function storeInjury(IncidentReport $report, array $injury): void
    {
        if (
            blank($injury['injury_category_id'] ?? null) &&
            blank($injury['body_part_id'] ?? null) &&
            blank($injury['description'] ?? null)
        ) {
            return;
        }

        IncidentInjury::query()->create([
            'incident_report_id' => $report->id,
            'injury_category_id' => $injury['injury_category_id'] ?? null,
            'body_part_id' => $injury['body_part_id'] ?? null,
            'description' => $injury['description'] ?? null,
        ]);
    }
}
