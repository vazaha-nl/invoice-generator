<?php

namespace App\EBoekhouden\Models;

use App\EBoekhouden\Enums\Unit;
use App\EBoekhouden\Enums\VatCode;

class InvoiceLine extends Model
{
    public float $amount;
    public Unit $unit;
    public string $code = 'NONE';
    public string $description;
    public float $pricePerUnit;
    public VatCode $vatCode;
    public string $contraAccountCode;

    public function toArray(): object
    {
        return (object)[
            'Aantal' => $this->amount,
            'Eenheid' => $this->unit->value,
            'Code' => $this->code,
            'Omschrijving' => $this->description,
            'PrijsPerEenheid' => $this->pricePerUnit,
            'BTWCode' => $this->vatCode->value,
            'BTWPercentage' => 19, // TODO FIX
            'TegenrekeningCode' => $this->contraAccountCode,
            'KostenplaatsID' => 0,
        ];
    }
}
