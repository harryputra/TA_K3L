<?php

namespace App\Http\Requests\Incident;

use App\Models\IncidentReport;
use Illuminate\Foundation\Http\FormRequest;

class VerifyIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var IncidentReport|null $report */
        $report = $this->route('incidentReport');

        return $report !== null && $this->user()?->can('verify', $report) === true;
    }

    public function rules(): array
    {
        return [
            'verification_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
