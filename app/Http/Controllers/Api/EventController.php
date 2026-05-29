<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use App\Http\Requests\Event\StoreEventRequest;
use App\Http\Requests\Event\UpdateEventRequest;
use App\Http\Resources\EventResource;
use App\Domain\Event\Services\EventService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class EventController extends BaseController
{
        public function __construct(
        private readonly EventService $service
    ) {}

    public function index()
    {
        try
        {
            return EventResource::collection(
                $this->service->list()
            );
        }
        catch (\Exception $e)
        {
            Log::error('Event listing error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function show(int $id)
    {
        try
        {
            return new EventResource(
                $this->service->get($id)
            );
        }
        catch (\Exception $e)
        {
            Log::error('Event show error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function seatMap(int $id)
    {
        try
        {
            return response()->json([
                'success' => true,
                'data' => $this->service->seatMap($id),
            ]);
        }
        catch (\Exception $e)
        {
            Log::error('Event seat map error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function store(StoreEventRequest $request)
    {
        try
        {
            $event = $this->service->create(
                $request->validated(),
                auth()->guard()->id()
            );

            return new EventResource($event);
        }
        catch (\Exception $e)
        {
            Log::error('Event creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }   
    }

    public function update(UpdateEventRequest $request, int $id)
    {
        try
        {
            $event = $this->service->get($id);

            return new EventResource(
                $this->service->update($event, $request->validated())
            );
        }
        catch (\Exception $e)
        {
            Log::error('Event update error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try
        {
            $event = $this->service->get($id);

            $this->service->delete($event);

            return response()->json([
                'success' => true,
                'message' => 'Event deleted successfully',
            ]);
        }
        catch (\Exception $e)
        {
            Log::error('Event delete error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => env('APP_DEBUG', false) ? $e->getMessage() : null,
            ], 500);
        }
    }
}
