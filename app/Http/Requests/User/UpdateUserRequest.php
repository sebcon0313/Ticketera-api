<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'sometimes|required|string|min:10|max:100',
            'email' => [
                'sometimes',
                'required',
                'string',
                'email',
                'min:10',
                'max:50',
                Rule::unique('users')->ignore($this->user()->id),
            ],
            'password' => 'sometimes|required|string|min:5|max:30|confirmed',
        ];
    }
}