<?php

namespace App\Http\Requests\Event;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEventRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'venue_id' => 'sometimes|exists:venues,id',
            'title' => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'starts_at' => 'sometimes|date',
            'ends_at' => 'nullable|date',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation error',
            'errors' => $validator->errors(),
        ], 422));
    }
}