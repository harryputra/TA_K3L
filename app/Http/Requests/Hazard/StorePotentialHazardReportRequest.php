<?php

namespace App\Http\Requests\Hazard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePotentialHazardReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_email' => ['required', 'email:rfc', 'max:150'],
            'reporter_whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
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
            'reporter_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'attachments.max' => 'Jumlah lampiran maksimal 3 file.',
            'attachments.*.mimes' => 'Lampiran hanya boleh berupa JPG, JPEG, atau PNG.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->user()) {
            return;
        }

        $phone = $this->user()->phone;
        $whatsapp = $this->input('reporter_whatsapp') ?: (
            $phone && preg_match('/^[0-9+\-\s()]+$/', $phone) ? $phone : '0'
        );

        $this->merge([
            'reporter_name' => $this->input('reporter_name') ?: $this->user()->name,
            'reporter_email' => $this->input('reporter_email') ?: $this->user()->email,
            'reporter_whatsapp' => $whatsapp,
        ]);
    }
}
