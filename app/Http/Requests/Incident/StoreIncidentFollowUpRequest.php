<?php

namespace App\Http\Requests\Incident;

use App\Models\IncidentReport;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentFollowUpRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var IncidentReport|null $report */
        $report = $this->route('incidentReport');

        return $report !== null
            && $this->user()?->can('addFollowUp', $report) === true;
    }

    public function rules(): array
    {
        return [
            'action_taken' => ['required', 'string', 'min:10', 'max:5000'],
            'action_owner_id' => ['nullable', 'integer', 'exists:users,id'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['open', 'in_progress', 'done', 'cancelled'])],
        ];
    }
}
