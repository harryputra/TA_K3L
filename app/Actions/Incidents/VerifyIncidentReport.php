<?php

namespace App\Actions\Incidents;

use App\Models\IncidentReport;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB;

class VerifyIncidentReport
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {
    }

    public function handle(IncidentReport $incidentReport, int $verifierId, ?string $note = null): IncidentReport
    {
        return DB::transaction(function () use ($incidentReport, $verifierId, $note) {
            $previousStatus = $incidentReport->status;

            $incidentReport->update([
                'status' => 'verified',
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

            return $incidentReport->fresh([
                'category',
                'location',
                'reporter',
                'statusHistories.changer',
            ]);
        });
    }
}
