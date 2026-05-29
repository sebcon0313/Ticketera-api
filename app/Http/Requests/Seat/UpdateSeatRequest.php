<?php

namespace App\Http\Requests\Seat;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSeatRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'section_id' => ['sometimes', 'integer', 'exists:sections,id'],
            'row_label' => ['sometimes', 'string', 'max:50'],
            'seat_number' => ['sometimes', 'string', 'max:50'],
        ];
    }
}
