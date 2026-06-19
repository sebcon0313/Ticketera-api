<?php

namespace App\Http\Requests\Booking;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class PayBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            //'payment_method' => 'required|string|max:50',
            //'payment_result' => 'nullable|in:exitoso,fallido',
            'booking_id' => 'required|exists:bookings,id',
            'ticket_type' => 'required|in:tarjeta,cortesia,efectivo',
            'payment_method' => 'required|in:tarjeta,efectivo',
            'number_target' => 'nullable|string|max:100',
            'month_target' => 'nullable|string|max:100',
            'year_target' => 'nullable|string|max:100',
            'cvc_target' => 'nullable|string|max:100',
            'name_target' => 'nullable|string|max:100',
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