<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'available_seats' => ['required', 'integer', 'min:0'],
            'country_ids'     => ['required', 'array', 'min:1'],
            'country_ids.*'   => ['integer', 'distinct', 'exists:countries,id'],
        ];
    }
}
