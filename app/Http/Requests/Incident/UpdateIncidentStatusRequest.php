<?php

namespace App\Http\Requests\Incident;

use App\Models\IncidentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var IncidentReport|null $report */
        $report = $this->route('incidentReport');

        return $report !== null
            && $this->user()?->can('manageProgress', $report) === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in($this->allowedStatuses())],
            'status_note' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Perubahan status yang dipilih tidak valid untuk kondisi laporan saat ini.',
        ];
    }

    protected function allowedStatuses(): array
    {
        /** @var IncidentReport|null $report */
        $report = $this->route('incidentReport');

        if (! $report) {
            return [];
        }

        return match ($report->status) {
            'submitted' => ['rejected'],
            'verified' => ['investigating', 'resolved', 'rejected'],
            'investigating' => ['resolved', 'rejected'],
            'resolved' => ['closed', 'investigating'],
            'rejected' => ['submitted'],
            default => [],
        };
    }
}
