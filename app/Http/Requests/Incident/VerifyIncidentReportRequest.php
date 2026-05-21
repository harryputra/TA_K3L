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
            'verified_location_id' => ['nullable', 'integer', 'exists:locations,id'],
            'verified_specific_location' => ['nullable', 'string', 'max:255'],
            'verified_latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'verified_longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'verified_location_accuracy' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'verification_note' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
