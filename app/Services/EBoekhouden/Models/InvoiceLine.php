<?php

namespace App\Services\EBoekhouden\Models;

use App\Collections\TimeEntryCollection;
use App\Services\EBoekhouden\Enums\Unit;
use App\Services\EBoekhouden\Enums\VatCode;

class InvoiceLine extends Model
{
    public float $amount;
    public Unit $unit;
    public string $code = 'NONE';
    public string $description;
    public float $pricePerUnit;
    public VatCode $vatCode;
    public string $contraAccountCode;

    public static function fromTimeEntryCollection(TimeEntryCollection $timeEntries)
    {
        $invoiceLine = new static();
        $invoiceLine->amount = $timeEntries->getRoundedDurationInHours();
        $invoiceLine->unit = Unit::HOUR;
        $invoiceLine->description = $timeEntries->getDescription();
        $invoiceLine->pricePerUnit = 90.00; // TODO should be variable? from db?
        $invoiceLine->vatCode = VatCode::HIGH_SALE_21;
        $invoiceLine->contraAccountCode = '8000'; // TODO make variable

        return $invoiceLine;
    }

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
