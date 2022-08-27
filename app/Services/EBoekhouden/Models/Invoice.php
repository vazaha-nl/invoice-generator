<?php

namespace App\Services\EBoekhouden\Models;

use App\Collections\TimeEntryCollection;
use App\Services\EBoekhouden\Enums\Unit;
use App\Services\EBoekhouden\Enums\VatCode;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Invoice extends Model
{
    // TODO autogenerate correctly
    // default auto generation of eboekhouden sucks
    public string $number = '99999999999';

    public string $relationCode = 'RISK';

    public Carbon $date;

    public int $paymentTerm = 14;

    // TODO in config
    public string $template = 'Vazaha factuur Nederlands';

    public Collection $lines;

    public function __construct()
    {
        $this->date = Carbon::today();
    }

    public function generateLines(Collection $timeEntries)
    {
        $this->lines = $timeEntries->groupBy('project')
            ->mapInto(TimeEntryCollection::class)
            ->map(function (TimeEntryCollection $timeEntries, $project) {
                $invoiceLine = new InvoiceLine();
                $invoiceLine->amount = $timeEntries->getRoundedDurationInHours();
                $invoiceLine->unit = Unit::Hour;
                $invoiceLine->description = $timeEntries->getDescription();
                $invoiceLine->pricePerUnit = 90.00; // TODO should be variable?
                $invoiceLine->vatCode = VatCode::HIGH_SALE_21;
                $invoiceLine->contraAccountCode = '8000'; // TODO make variable

                return $invoiceLine;
            })
            ->sortBy('description')
            ->values();
    }

    public function toArray(): array
    {
        return [
            'oFact' => [
                'Factuurnummer' => $this->number,
                'Relatiecode' => $this->relationCode,
                'Datum' => $this->formatDate($this->date),
                'Factuursjabloon' => $this->template,
                'Betalingstermijn' => $this->paymentTerm,
                'PerEmailVerzenden' => false,
                'AutomatischeIncasso' => false,
                'IncassoMachtigingDatumOndertekening' => '2022-01-01',
                'IncassoMachtigingFirst' => false,
                'InBoekhoudingPlaatsen' => false,
                'Regels' => [
                    'cFactuurRegel' => $this->lines->map(function (InvoiceLine $invoiceLine) {
                        return $invoiceLine->toArray();
                    })->toArray(),
                ]
            ],
        ];
    }
}
