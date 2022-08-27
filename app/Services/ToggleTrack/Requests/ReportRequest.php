<?php

namespace App\Services\ToggleTrack\Requests;

use Carbon\Carbon;
use DateTimeInterface;

abstract class ReportRequest extends Request
{
    protected Carbon|null $since;

    protected Carbon|null $until;

    public function setSince(string|DateTimeInterface $date): static
    {
        $this->since = Carbon::parse($date);

        return $this;
    }

    protected function getSince(): string|null
    {
        if (!isset($this->since)) {
            return null;
        }

        return $this->since->format('Y-m-d');
    }

    protected function getUntil(): string|null
    {
        if (!isset($this->until)) {
            return null;
        }

        return $this->until->format('Y-m-d');
    }

    public function setUntil(string|DateTimeInterface $date): static
    {
        $this->until = Carbon::parse($date);

        return $this;
    }

    public function getQueryParams(): array
    {
        return array_merge([
            'since' => $this->getSince(),
            'until' => $this->getUntil(),
        ], parent::getQueryParams());
    }
}
