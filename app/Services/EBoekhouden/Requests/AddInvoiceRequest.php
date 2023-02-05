<?php

namespace App\Services\EBoekhouden\Requests;

use App\Services\EBoekhouden\Models\Invoice;

class AddInvoiceRequest extends Request
{
    public function __construct(public Invoice $invoice)
    {
        //
    }

    public function toArray(): array
    {
        return [
            'oFact' => $this->invoice->toArray(),
        ];
    }

    public function getResultPath(): null
    {
        // TODO CHECK what is the return value/format for this call? it is not documented.
        return null;
    }

    public function getMethodName(): string
    {
        return 'AddFactuur';
    }
}
