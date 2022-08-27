<?php

namespace App\Collections;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class TimeEntryCollection extends Collection
{
    public function getUniqueDates(): Collection
    {
        return $this->groupBy(fn (TimeEntry $timeEntry) => $timeEntry->start->format('Y-m-d'))
            ->keys()
            ->unique()
            ->sort()
            ->map(fn (string $date) => Carbon::parse($date));
    }

    public function getDurationInSeconds(): int
    {
        return $this->sum(function (TimeEntry $timeEntry) {
            return $timeEntry->getDurationInSeconds();
        });
    }

    public function getRoundedDurationInHours(): float
    {
        $seconds = $this->getDurationInSeconds();
        $hours = $seconds / (60 * 60);

        return round($hours * 4) / 4;
    }

    public function getDateString(): string
    {
        return $this->getUniqueDates()
            ->chunkWhile(function (Carbon $date, $key, $chunk) {
                return $date->diffInDays($chunk->last(), true) === 1;
            })
            ->map(function (Collection $dates) {
                /** @var Carbon $firstDate */
                $firstDate = $dates->first();
                /** @var Carbon $lastDate */
                $lastDate = $dates->last();

                if ($dates->count() === 1) {
                    return $firstDate->format('d/m');
                }

                if ($firstDate->month === $lastDate->month) {
                    return sprintf('%02d-%02d/%02d', $firstDate->day, $lastDate->day, $firstDate->month);
                }

                return $firstDate->format('d/m') . ' - ' . $lastDate->format('d/m');
            })
            ->join(', ');
    }

    public function getDescription(): string
    {
        return sprintf('%s (%s)', $this->first()->projectName, $this->getDateString());
    }
}
