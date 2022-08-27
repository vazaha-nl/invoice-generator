<?php

namespace App\Services\ToggleTrack\Requests;

use Carbon\Carbon;
use DateTimeInterface;

abstract class ReportRequest extends Request
{
    protected Carbon|null $since;

    protected Carbon|null $until;

    public function since(string|DateTimeInterface $date): static
    {
        $this->since = Carbon::parse($date);

        return $this;
    }

    public function until(string|DateTimeInterface $date): static
    {
        $this->until = Carbon::parse($date);

        return $this;
    }

    public function getQueryParams(): array
    {
        return array_merge([
            'since' => $this->since->format('Y-m-d'),
            'until' => $this->until->format('Y-m-d'),
        ], parent::getQueryParams());
    }
}
