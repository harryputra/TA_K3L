<?php

namespace App\Http\Requests\Hazard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePotentialHazardStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isSatgas() === true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(['reviewed', 'resolved'])],
            'response_note' => ['required', 'string', 'max:2000'],
        ];
    }
}
