<?php

namespace App\Actions\Incidents;

use App\Models\IncidentAttachment;
use App\Models\IncidentReport;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateIncidentReport
{
    public function handle(array $validated, int $reporterId): IncidentReport
    {
        return DB::transaction(function () use ($validated, $reporterId) {
            $attachments = $validated['attachments'] ?? [];
            unset($validated['attachments']);

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

            $report->statusHistories()->create([
                'from_status' => null,
                'to_status' => 'submitted',
                'changed_by' => $reporterId,
                'change_note' => 'Laporan insiden dibuat oleh pelapor.',
                'created_at' => now(),
            ]);

            return $report->load(['category', 'location', 'attachments']);
        });
    }

    protected function generateReportNumber(): string
    {
        return 'INC-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
    }

    protected function storeAttachment(IncidentReport $report, UploadedFile $attachment, int $reporterId): void
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
}
