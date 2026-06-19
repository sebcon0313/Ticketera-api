<?php

namespace App\Domain\Payments\Repositories;

use App\Domain\Payments\Models\Payment;

class PaymentRepository
{
    public function create(array $data): Payment
    {
        return Payment::create($data);
    }
}