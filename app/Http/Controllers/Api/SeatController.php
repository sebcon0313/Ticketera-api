<?php

namespace App\Http\Controllers\Api;

use App\Domain\Seat\Services\SeatService;
use App\Http\Requests\Seat\BulkGenerateSeatRequest;
use App\Http\Requests\Seat\StoreSeatRequest;
use App\Http\Requests\Seat\UpdateSeatRequest;
use App\Http\Resources\SeatResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class SeatController extends BaseController
{
    public function __construct(
        private readonly SeatService $service
    ) {}

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 15);

            return SeatResource::collection(
                $this->service->list($perPage)
            );
        } catch (\Exception $e) {
            Log::error('Seat listing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function listBySection(int $sectionId)
    {
        try {
            return SeatResource::collection(
                $this->service->listBySection($sectionId)
            );
        } catch (\Exception $e) {
            Log::error('Seat list by section error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            return new SeatResource(
                $this->service->findById($id)
            );
        } catch (\Exception $e) {
            Log::error('Seat show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(StoreSeatRequest $request)
    {
        try {
            return new SeatResource(
                $this->service->create($request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Seat creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function bulkGenerate(BulkGenerateSeatRequest $request): JsonResponse
    {
        try {
            $result = $this->service->bulkGenerate($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Seats generated successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Seat bulk generation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(UpdateSeatRequest $request, int $id)
    {
        try {
            $seat = $this->service->findById($id);

            return new SeatResource(
                $this->service->update($seat, $request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Seat update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $seat = $this->service->findById($id);

            $this->service->delete($seat);

            return response()->json([
                'success' => true,
                'message' => 'Seat deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Seat delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}
