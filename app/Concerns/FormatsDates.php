<?php

namespace App\Services\ToggleTrack\Concerns;

use Carbon\CarbonImmutable;
use Carbon\Exceptions\InvalidFormatException;

// TODO make generic with configurable return format
trait FormatsDates
{
    /**
     * @param string|\DateTimeInterface|null $date
     * @return null|string
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function formatDate($date): ?string
    {
        if ($date === null) {
            return null;
        }

        try {
            return CarbonImmutable::parse($date)->format('Y-m-d');
        } catch (InvalidFormatException $e) {
            report($e);

            return null;
        }
    }
}
