<?php

namespace App\Services\EBoekhouden\Models;

use App\Collections\TimeEntryCollection;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Invoice extends Model
{
    // TODO autogenerate correctly
    // default auto generation of eboekhouden sucks
    public string $number = '2022090014';

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
        $this->lines = $timeEntries->groupBy('projectName')
            ->mapInto(TimeEntryCollection::class)
            ->map(function (TimeEntryCollection $timeEntries) {
                return InvoiceLine::fromTimeEntryCollection($timeEntries);
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
                'IncassoMachtigingDatumOndertekening' => $this->formatDate(Carbon::today()),
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
