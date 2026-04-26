<?php

namespace App\Actions\Incidents;

use App\Models\IncidentReport;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\DB;

class UpdateIncidentReportStatus
{
    public function __construct(
        protected ActivityLogger $activityLogger,
    ) {
    }

    public function handle(IncidentReport $incidentReport, string $status, int $userId, ?string $note = null): IncidentReport
    {
        return DB::transaction(function () use ($incidentReport, $status, $userId, $note) {
            $previousStatus = $incidentReport->status;

            $incidentReport->update([
                'status' => $status,
                'closed_by' => $status === 'closed' ? $userId : $incidentReport->closed_by,
                'closed_at' => $status === 'closed' ? now() : $incidentReport->closed_at,
            ]);

            $incidentReport->statusHistories()->create([
                'from_status' => $previousStatus,
                'to_status' => $status,
                'changed_by' => $userId,
                'change_note' => $note ?: $this->defaultNote($status),
                'created_at' => now(),
            ]);

            $this->activityLogger->log(
                $incidentReport->reported_by,
                $userId,
                'incident_status_updated',
                'Status laporan insiden diperbarui',
                $note ?: $this->defaultNote($status),
                $incidentReport,
                [
                    'from_status' => $previousStatus,
                    'to_status' => $status,
                    'report_number' => $incidentReport->report_number,
                ],
            );

            return $incidentReport->fresh([
                'category',
                'location',
                'reporter',
                'victim',
                'attachments',
                'statusHistories.changer',
                'followUps.actionOwner',
                'followUps.creator',
            ]);
        });
    }

    protected function defaultNote(string $status): string
    {
        return match ($status) {
            'investigating' => 'Laporan masuk ke tahap investigasi oleh Satgas.',
            'resolved' => 'Tindakan perbaikan utama sudah dilakukan dan menunggu penutupan.',
            'closed' => 'Laporan ditutup oleh Satgas.',
            'rejected' => 'Laporan ditandai perlu perbaikan atau klarifikasi.',
            'submitted' => 'Laporan dikembalikan ke status submitted untuk proses ulang.',
            default => 'Status laporan diperbarui oleh Satgas.',
        };
    }
}
