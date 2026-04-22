<?php

namespace App\Actions\Incidents;

use App\Models\IncidentReport;
use Illuminate\Support\Facades\DB;

class VerifyIncidentReport
{
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

            return $incidentReport->fresh([
                'category',
                'location',
                'reporter',
                'statusHistories.changer',
            ]);
        });
    }
}
