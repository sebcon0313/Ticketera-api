<?php

namespace App\Domain\Invoice\Repositories;

use App\Domain\Invoice\Models\Invoice;

class InvoiceRepository implements InvoiceRepositoryInterface
{
    public function create(array $data): Invoice
    {
        return Invoice::create($data);
    }
}