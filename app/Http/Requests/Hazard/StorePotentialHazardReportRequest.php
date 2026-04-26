<?php

namespace App\Http\Requests\Hazard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePotentialHazardReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'hazard_type' => ['required', Rule::in(['lingkungan', 'peralatan', 'listrik', 'zat-kimia'])],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'specific_location' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'attachments' => ['nullable', 'array', 'max:3'],
            'attachments.*' => ['file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'hazard_type.in' => 'Jenis temuan yang dipilih tidak valid.',
            'location_id.exists' => 'Lokasi yang dipilih tidak valid.',
            'attachments.max' => 'Jumlah lampiran maksimal 3 file.',
            'attachments.*.mimes' => 'Lampiran hanya boleh berupa JPG, JPEG, atau PNG.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }
}
