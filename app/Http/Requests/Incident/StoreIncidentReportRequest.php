<?php

namespace App\Http\Requests\Incident;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'incident_category_id' => ['required', 'integer', 'exists:incident_categories,id'],
            'injury_category_id' => ['nullable', 'integer', 'exists:injury_categories,id'],
            'body_part_id' => ['nullable', 'integer', 'exists:body_parts,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'incident_time' => ['nullable', 'date_format:H:i'],
            'severity_level' => ['required', Rule::in(['low', 'medium', 'high', 'critical'])],
            'victim_type' => ['required', Rule::in(['self', 'other'])],
            'victim_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'chronology' => ['required', 'string', 'min:30'],
            'cause' => ['nullable', 'string', 'max:5000'],
            'initial_action' => ['nullable', 'string', 'max:5000'],
            'impact' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png,pdf,doc,docx', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'incident_category_id.exists' => 'Kategori insiden yang dipilih tidak valid.',
            'location_id.exists' => 'Lokasi kejadian yang dipilih tidak valid.',
            'incident_date.before_or_equal' => 'Tanggal kejadian tidak boleh melebihi hari ini.',
            'chronology.min' => 'Kronologi minimal 30 karakter agar laporan cukup informatif.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('victim_type') === 'self') {
            $this->merge([
                'victim_user_id' => $this->user()?->id,
            ]);
        }
    }
}
