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

    // TODO make more generic with interface
    public static function fromTimeEntryCollection(TimeEntryCollection $timeEntries): static
    {
        $invoiceLine = new static();
        $invoiceLine->amount = $timeEntries->getRoundedDurationInHours();
        $invoiceLine->unit = Unit::HOUR; // TODO refactor so that this can be multi language
        $invoiceLine->description = $timeEntries->getDescription();
        $invoiceLine->pricePerUnit = $timeEntries->getRate() ?? 90.00; // TODO no fallback here, must come from upstream
        $invoiceLine->vatCode = VatCode::HIGH_SALE_21; // TODO get from timeEntries? refactor!
        $invoiceLine->contraAccountCode = '8000'; // TODO make variable / in config?

        return $invoiceLine;
    }

    public function toArray(): array
    {
        return [
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

    public function toString(): string
    {
        return sprintf(
            '%.2f %s %s @ %.2f : %.2f',
            $this->amount,
            $this->unit->value,
            $this->description,
            $this->pricePerUnit,
            $this->pricePerUnit * $this->amount,
        );
    }
}
