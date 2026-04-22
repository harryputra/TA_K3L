<?php

namespace App\Http\Requests\Admin;

use App\Models\IncidentCategory;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateIncidentCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        /** @var IncidentCategory|null $incidentCategory */
        $incidentCategory = $this->route('incidentCategory');

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('incident_categories', 'name')->ignore($incidentCategory?->id),
            ],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
