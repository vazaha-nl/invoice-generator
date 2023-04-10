<?php

namespace App\Services\EBoekhouden\Models;

use App\Collections\TimeEntryCollection;
use App\Concerns\FormatsDates;
use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class Invoice extends Model
{
    use FormatsDates;

    public string $number;

    public string $relationCode;

    public Carbon $date;

    public int $paymentTerm;

    public string $template;

    public Collection $lines;

    public function __construct()
    {
        $this->template = config('e_boekhouden.invoice_template');
        $this->paymentTerm = config('e_boekhouden.invoice_payment_term');
    }

    public static function fromArray(array $invoiceData): static
    {
        // TODO
        return (new static())
            ->setNumber($invoiceData['Factuurnummer'])
            ->setDate($invoiceData['Datum'])
            ->set
        ;
    }

    public function getDate(): Carbon
    {
        if (!isset($this->date)) {
            return Carbon::today();
        }

        return $this->date;
    }

    public function setDate($date): self
    {
        $this->date = Carbon::parse($date);

        return $this;
    }

    public function getTemplate(): string
    {
        if (!isset($this->template)) {
            return config('e_boekhouden.invoice_template');
        }

        return $this->template;
    }

    public function setTemplate(string $template): self
    {
        $this->template = $template;

        return $this;
    }

    public function getPaymentTerm(): int
    {
        if (!isset($this->paymentTerm)) {
            return config('e_boekhouden.invoice_paymentTerm');
        }

        return $this->paymentTerm;
    }

    public function setPaymentTerm(int $paymentTerm): self
    {
        $this->paymentTerm = $paymentTerm;

        return $this;
    }


    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function setRelationCode(string $relationCode): self
    {
        $this->relationCode = $relationCode;

        return $this;
    }

    public function generateLines(Collection $timeEntries): self
    {
        $this->lines = $timeEntries
            ->groupBy(
                fn (TimeEntry $timeEntry) => $timeEntry->project->name
            )
            ->mapInto(TimeEntryCollection::class)
            ->map(function (TimeEntryCollection $timeEntries) {
                return InvoiceLine::fromTimeEntryCollection($timeEntries);
            })
            ->sortBy('description')
            ->values();

        return $this;
    }

    public function toArray(): array
    {
        return [
            'Factuurnummer' => $this->number,
            'Relatiecode' => $this->relationCode,
            'Datum' => $this->formatDate($this->getDate()),
            'Factuursjabloon' => $this->getTemplate(),
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
        ];
    }
}
