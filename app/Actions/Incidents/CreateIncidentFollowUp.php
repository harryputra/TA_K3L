<?php

namespace App\Actions\Incidents;

use App\Models\IncidentFollowUp;
use App\Models\IncidentReport;
use Illuminate\Support\Facades\DB;

class CreateIncidentFollowUp
{
    public function handle(IncidentReport $incidentReport, array $validated, int $userId): IncidentFollowUp
    {
        return DB::transaction(function () use ($incidentReport, $validated, $userId) {
            $previousStatus = $incidentReport->status;

            $followUp = $incidentReport->followUps()->create([
                ...$validated,
                'completed_at' => ($validated['status'] ?? null) === 'done' ? now() : null,
                'created_by' => $userId,
            ]);

            if (
                in_array($incidentReport->status, ['verified', 'submitted'], true) &&
                in_array($followUp->status, ['open', 'in_progress', 'done'], true)
            ) {
                $incidentReport->update([
                    'status' => 'investigating',
                ]);

                $incidentReport->statusHistories()->create([
                    'from_status' => $previousStatus,
                    'to_status' => 'investigating',
                    'changed_by' => $userId,
                    'change_note' => 'Status diperbarui otomatis setelah tindak lanjut dicatat oleh Satgas.',
                    'created_at' => now(),
                ]);
            }

            return $followUp->fresh(['actionOwner', 'creator']);
        });
    }
}
