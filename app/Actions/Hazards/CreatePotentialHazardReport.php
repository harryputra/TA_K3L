<?php

namespace App\Actions\Hazards;

use App\Models\PotentialHazardAttachment;
use App\Models\PotentialHazardReport;
use App\Support\ActivityLogger;
use App\Support\WhatsAppReportNotifier;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreatePotentialHazardReport
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected WhatsAppReportNotifier $whatsAppReportNotifier,
    ) {
    }

    public function handle(array $validated, ?int $reporterId = null): PotentialHazardReport
    {
        return DB::transaction(function () use ($validated, $reporterId) {
            $attachments = $validated['attachments'] ?? [];
            unset($validated['attachments']);

            $report = PotentialHazardReport::query()->create([
                ...$validated,
                'report_number' => $this->generateReportNumber(),
                'reported_by' => $reporterId,
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            foreach ($attachments as $attachment) {
                $this->storeAttachment($report, $attachment, $reporterId);
            }

            if ($reporterId !== null) {
                $this->activityLogger->log(
                    $reporterId,
                    $reporterId,
                    'hazard_created',
                    'Hazard report berhasil dikirim',
                    "Hazard {$report->report_number} dengan judul \"{$report->title}\" sedang menunggu review Satgas.",
                    $report,
                    [
                        'status' => 'submitted',
                        'report_number' => $report->report_number,
                    ],
                );
            }

            $this->whatsAppReportNotifier->hazardCreated($report);
            $this->whatsAppReportNotifier->satgasHazardCreated($report);

            return $report->load(['location', 'attachments']);
        });
    }

    protected function generateReportNumber(): string
    {
        return 'HZD-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(5));
    }

    protected function storeAttachment(PotentialHazardReport $report, UploadedFile $attachment, ?int $reporterId): void
    {
        $path = $attachment->store('potential-hazard-attachments', 'public');

        PotentialHazardAttachment::query()->create([
            'potential_hazard_report_id' => $report->id,
            'file_name' => $attachment->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $attachment->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $attachment->getSize(),
            'uploaded_by' => $reporterId,
        ]);
    }
}
