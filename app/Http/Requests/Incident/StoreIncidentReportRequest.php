<?php

namespace App\Http\Requests\Incident;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidentReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'reporter_name' => ['required', 'string', 'max:150'],
            'reporter_email' => ['required', 'email:rfc', 'max:150'],
            'reporter_whatsapp' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
            'incident_category_id' => ['nullable', 'integer', 'exists:incident_categories,id'],
            'location_id' => ['required', 'integer', 'exists:locations,id'],
            'incident_date' => ['required', 'date', 'before_or_equal:today'],
            'severity_level' => ['nullable', Rule::in(['low', 'medium', 'high', 'critical'])],
            'victim_type' => ['nullable', Rule::in(['self', 'other'])],
            'victim_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'chronology' => ['required', 'string', 'min:30'],
            'cause' => ['nullable', 'string', 'max:5000'],
            'initial_action' => ['nullable', 'string', 'max:5000'],
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
            'reporter_whatsapp.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, +, -, dan tanda kurung.',
            'chronology.min' => 'Kronologi minimal 30 karakter agar laporan cukup informatif.',
            'attachments.*.max' => 'Ukuran tiap lampiran maksimal 5 MB.',
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->user()) {
            $this->merge([
                'reporter_name' => $this->input('reporter_name') ?: $this->user()->name,
                'reporter_email' => $this->input('reporter_email') ?: $this->user()->email,
                'reporter_whatsapp' => $this->input('reporter_whatsapp') ?: ($this->user()->phone ?? '-'),
            ]);
        }

        if ($this->user() && $this->input('victim_type') === 'self') {
            $this->merge([
                'victim_user_id' => $this->user()?->id,
            ]);
        }
    }
}
