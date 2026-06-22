<?php 

namespace App\Domain\Ticket\Services;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Domain\Ticket\Repositories\ITicketRepository;

class TicketService
{
    public function __construct(private ITicketRepository $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function updateQrCodes(array $tickets): int
    {
        foreach ($tickets as $ticket) {
            $ticketFind = $this->ticketRepository->findById($ticket['ticket_id']);

            if (!$ticketFind) {
                throw new NotFoundHttpException(
                    "Ticket id " . $ticket['ticket_id'] . " not found"
                );
            }
        }

        return $this->ticketRepository->updateQrCodes(
            $tickets, 
            auth()->guard()->user()->id
        );
    }
}