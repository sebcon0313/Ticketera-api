<?php

namespace App\Http\Controllers\Api;

use App\Domain\Event_Sections\Dtos\EventSectionsDTO;
use App\Domain\Event_Sections\Services\EventSectionsService;
use App\Http\Requests\Event_Sections\StoreEventSectionsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class EventSectionsController extends BaseController
{
    public function __construct(
        private readonly EventSectionsService $service,
    ) {}

    public function store(StoreEventSectionsRequest $request): JsonResponse
    {
        try {
            $result = $this->service->storePrices(
                EventSectionsDTO::fromArray($request->validated())
            );

            return response()->json([
                'success' => true,
                'message' => 'Event section prices saved successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('Event sections creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Server error',
                'error' => 'Error creating Event Section',
            ], 500);
        }
    }
}