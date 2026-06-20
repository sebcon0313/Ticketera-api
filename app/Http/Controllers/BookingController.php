<?php

namespace App\Http\Controllers;

use App\Domain\Booking\DTOs\BookingPayDTO;
use App\Domain\Booking\Services\BookingService;
use App\Http\Requests\Booking\PayBookingRequest;
use App\Http\Requests\Booking\StoreBookingRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Http\Resources\BookingSummaryResource;
use App\Domain\Booking\DTOs\BookingSummaryDTO;

class BookingController extends BaseController
{
    public function __construct(
        private readonly BookingService $service
    ) {}

    public function store(StoreBookingRequest $request): JsonResponse
    {
        try {
            $result = $this->service->reserveSeats(
                auth()->guard()->id(),
                (int) $request->validated('event_id'),
                $request->validated('seat_ids')
            );

            return response()->json([
                'success' => true,
                'booking_id' => $result['booking_id'],
                'reference' => $result['reference'],
                'total' => $result['total'],
                'expires_at' => $result['expires_at'],
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Booking creation error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function pay(PayBookingRequest $request): JsonResponse
    {
        try {
            $result = $this->service->payBooking(
                BookingPayDTO::fromArray($request->validated())
            );

            return response()->json($result, $result['success'] ? 200 : 422);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Booking payment error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function summary(int $bookingId)
    {
        try{
            $dto = $this->service->getBookingSummary(
                $bookingId,
                auth()->guard()->id(),
            );

            return new BookingSummaryResource($dto);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Booking payment error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}