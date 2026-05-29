<?php

namespace App\Http\Requests\Section;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $sectionRoute = $this->route('section');
        $sectionId = is_object($sectionRoute) ? $sectionRoute->id : $sectionRoute;

        return [
            'venue_id' => 'sometimes|exists:venues,id',
            'name' => 'sometimes|string|max:150',
            'code' => 'sometimes|string|max:50|unique:sections,code,' . $sectionId,
            'map_config' => 'nullable|array',
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