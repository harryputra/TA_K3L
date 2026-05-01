<?php

namespace App\Actions\Incidents;

use App\Models\IncidentReport;
use App\Support\ActivityLogger;
use App\Support\WhatsAppReportNotifier;
use Illuminate\Support\Facades\DB;

class VerifyIncidentReport
{
    public function __construct(
        protected ActivityLogger $activityLogger,
        protected WhatsAppReportNotifier $whatsAppReportNotifier,
    ) {
    }

    public function handle(IncidentReport $incidentReport, int $verifierId, ?string $note = null, array $classification = []): IncidentReport
    {
        return DB::transaction(function () use ($incidentReport, $verifierId, $note) {
            $previousStatus = $incidentReport->status;

            $incidentReport->update([
                'status' => 'verified',
                'injury_category_id' => $classification['injury_category_id'] ?? $incidentReport->injury_category_id,
                'body_part_id' => $classification['body_part_id'] ?? $incidentReport->body_part_id,
                'impact' => $classification['impact'] ?? $incidentReport->impact,
                'verified_by' => $verifierId,
                'verified_at' => now(),
            ]);

            $incidentReport->statusHistories()->create([
                'from_status' => $previousStatus,
                'to_status' => 'verified',
                'changed_by' => $verifierId,
                'change_note' => $note ?: 'Laporan telah diverifikasi oleh Satgas/Admin.',
                'created_at' => now(),
            ]);

            if ($incidentReport->reported_by !== null) {
                $this->activityLogger->log(
                    $incidentReport->reported_by,
                    $verifierId,
                    'incident_verified',
                    'Laporan insiden telah diverifikasi',
                    $note ?: "Laporan {$incidentReport->report_number} telah diverifikasi dan siap ditindaklanjuti.",
                    $incidentReport,
                    [
                        'from_status' => $previousStatus,
                        'to_status' => 'verified',
                        'report_number' => $incidentReport->report_number,
                    ],
                );
            }

            $this->whatsAppReportNotifier->incidentStatusUpdated(
                $incidentReport,
                'verified',
                $note ?: "Laporan {$incidentReport->report_number} telah diverifikasi dan siap ditindaklanjuti.",
            );

            return $incidentReport->fresh([
                'category',
                'location',
                'reporter',
                'statusHistories.changer',
            ]);
        });
    }
}
