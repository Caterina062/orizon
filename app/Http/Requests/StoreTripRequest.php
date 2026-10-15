<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'available_seats' => ['required', 'integer', 'min:0'],
            'country_ids'     => ['required', 'array', 'min:1'],
            'country_ids.*'   => ['integer', 'distinct', 'exists:countries,id'],
        ];
    }
}
