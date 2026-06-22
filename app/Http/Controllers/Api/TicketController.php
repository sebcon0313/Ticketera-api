<?php 

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controller as BaseController;
use App\Domain\Ticket\Services\TicketService;
use App\Http\Requests\Ticket\UpdateQrCodesRequest;
use Illuminate\Support\Facades\Log;

class TicketController extends BaseController
{
    public function __construct(
        private readonly TicketService $service
    ) {}

    public function updateQrCodes(UpdateQrCodesRequest $request)
    {
        try {
            $result = $this->service->updateQrCodes($request->validated('tickets'));

            return response()->json([
                'success' => true,
                'updated_count' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('QR code update error: ' . $e->getMessage(), [
                'exception' => $e
            ]);

            return response()->json([
                 'success' => false,
                 'message' => 'Server error during QR code update',
            ], 500);
        }
    }
}