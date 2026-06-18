<?php

namespace App\Http\Controllers\Api;

use App\Domain\Event\Services\EventService;
use App\Domain\Event_Seat\Services\EventSeatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;

class EventSeatController extends BaseController
{
    public function __construct(
        private readonly EventService $eventService,
        private readonly EventSeatService $eventSeatService,
    ) {}

    
}