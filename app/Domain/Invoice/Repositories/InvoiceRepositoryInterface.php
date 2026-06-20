<?php

namespace App\Domain\Invoice\Repositories;

use App\Domain\Invoice\Models\Invoice;

interface InvoiceRepositoryInterface
{
    public function create(array $data): Invoice;
}