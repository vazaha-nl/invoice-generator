<?php

namespace App\Services\EBoekhouden\Requests;

use Carbon\Carbon;
use DateTimeInterface;

class GetInvoicesRequest extends Request
{
    public function __construct(
        public ?string $invoiceNumber = null,
        public ?string $relationCode = null,
        public ?DateTimeInterface $from = null,
        public ?DateTimeInterface $to = null,
    )
    {
        $this->from ??= Carbon::parse('2020-01-01');
        $this->to ??= Carbon::now();
    }

    public function toArray(): array
    {
        return [
            'cFilter' => [
                'Factuurnummer' => $this->invoiceNumber,
                'Relatiecode' => $this->relationCode,
                'DatumVan' => $this->from->format('Y-m-d'),
                'DatumTm' => $this->to->format('Y-m-d'),
            ],
        ];
    }

    public function getResultPath(): string
    {
        return 'GetFacturenResult.Facturen.cFactuurList';
    }

    public function getMethodName(): string
    {
        return 'GetFacturen';
    }
}
