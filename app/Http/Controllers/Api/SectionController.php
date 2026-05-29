<?php

namespace App\Http\Controllers\Api;

use App\Domain\Section\Services\SectionService;
use App\Http\Requests\Section\StoreSectionRequest;
use App\Http\Requests\Section\UpdateSectionRequest;
use App\Http\Resources\SectionResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class SectionController extends BaseController
{
    public function __construct(
        private readonly SectionService $service
    ) {}

    public function index()
    {
        try {
            return SectionResource::collection(
                $this->service->list()
            );
        } catch (\Exception $e) {
            Log::error('Section listing error: ' . $e->getMessage());

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
            return new SectionResource(
                $this->service->findById($id)
            );
        } catch (\Exception $e) {
            Log::error('Section show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function listByVenue(int $venueId)
    {
        try {
            return SectionResource::collection(
                $this->service->listByVenueId($venueId)
            );
        } catch (\Exception $e) {
            Log::error('Section list by venue error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(StoreSectionRequest $request)
    {
        try {
            return new SectionResource(
                $this->service->create($request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Section creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function update(UpdateSectionRequest $request, int $id)
    {
        try {
            $section = $this->service->findById($id);

            return new SectionResource(
                $this->service->update($section, $request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Section update error: ' . $e->getMessage());

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
            $section = $this->service->findById($id);

            $this->service->delete($section);

            return response()->json([
                'success' => true,
                'message' => 'Section deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Section delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}
