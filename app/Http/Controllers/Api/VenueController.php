<?php

namespace App\Http\Controllers\Api;

use App\Domain\Venue\Services\VenueService;
use App\Http\Requests\Venue\StoreVenueRequest;
use App\Http\Requests\Venue\UpdateVenueRequest;
use App\Http\Resources\VenueResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class VenueController extends BaseController
{
    public function __construct(
        private readonly VenueService $service
    ) {}

    public function index(Request $request)
    {
        try {
            $perPage = (int) $request->integer('per_page', 15);

            return VenueResource::collection(
                $this->service->list($perPage)
            );
        } catch (\Exception $e) {
            Log::error('Venue listing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error getting Venues',
            ], 500);
        }
    }

    public function show(int $id)
    {
        try {
            return new VenueResource(
                $this->service->findById($id)
            );
        } catch (\Exception $e) {
            Log::error('Venue show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error getting venue',
            ], 500);
        }
    }

    public function store(StoreVenueRequest $request)
    {
        try {
            return new VenueResource(
                $this->service->create($request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Venue creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error creating venue',
            ], 500);
        }
    }

    public function update(UpdateVenueRequest $request, int $id)
    {
        try {
            $venue = $this->service->findById($id);

            return new VenueResource(
                $this->service->update($venue, $request->validated())
            );
        } catch (\Exception $e) {
            Log::error('Venue update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error updating venue',
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $venue = $this->service->findById($id);

            $this->service->delete($venue);

            return response()->json([
                'success' => true,
                'message' => 'Venue deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Venue delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error Deleting venue',
            ], 500);
        }
    }
}