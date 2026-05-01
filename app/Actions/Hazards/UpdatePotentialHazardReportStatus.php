<?php

namespace App\Actions\Hazards;

use App\Models\PotentialHazardReport;
use App\Support\ActivityLogger;
use App\Support\WhatsAppReportNotifier;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class UpdatePotentialHazardReportStatus
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected WhatsAppReportNotifier $whatsAppReportNotifier,
    ) {
    }

    public function handle(PotentialHazardReport $report, string $status, string $note, int $actorId): PotentialHazardReport
    {
        if (! in_array($status, $this->allowedTransitions($report->status), true)) {
            throw new InvalidArgumentException('Perubahan status hazard report tidak valid.');
        }

        return DB::transaction(function () use ($report, $status, $note, $actorId) {
            $attributes = [
                'status' => $status,
                'response_note' => $note,
            ];

            if ($status === 'reviewed') {
                $attributes['reviewed_by'] = $actorId;
                $attributes['reviewed_at'] = now();
            }

            if ($status === 'resolved') {
                $attributes['resolved_by'] = $actorId;
                $attributes['resolved_at'] = now();
                $attributes['reviewed_by'] = $report->reviewed_by ?? $actorId;
                $attributes['reviewed_at'] = $report->reviewed_at ?? now();
            }

            $report->update($attributes);

            if ($report->reported_by !== null) {
                $this->activityLogger->log(
                    $report->reported_by,
                    $actorId,
                    'hazard_status_updated',
                    'Status hazard report diperbarui',
                    $note,
                    $report,
                    [
                        'to_status' => $status,
                        'report_number' => $report->report_number,
                    ],
                );
            }

            $this->whatsAppReportNotifier->hazardStatusUpdated($report, $status, $note);

            return $report->fresh([
                'reporter.role',
                'location',
                'attachments',
                'reviewer',
                'resolver',
            ]);
        });
    }

    public function allowedTransitions(string $currentStatus): array
    {
        return match ($currentStatus) {
            'submitted' => ['reviewed', 'resolved'],
            'reviewed' => ['resolved'],
            default => [],
        };
    }
}
