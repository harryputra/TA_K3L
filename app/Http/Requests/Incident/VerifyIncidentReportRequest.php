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
            'injury_category_id' => ['nullable', 'integer', 'exists:injury_categories,id'],
            'body_part_id' => ['nullable', 'integer', 'exists:body_parts,id'],
            'impact' => ['nullable', 'string', 'max:5000'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
