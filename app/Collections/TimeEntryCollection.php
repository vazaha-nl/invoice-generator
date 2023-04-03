<?php

namespace App\Collections;

use App\Models\TimeEntry;
use Carbon\Carbon;
use Illuminate\Support\Collection;

// TODO make interface and think of a name for this interface
class TimeEntryCollection extends Collection
{
    /** @return \Illuminate\Support\Collection|array<array-key, \Carbon\Carbon>  */
    public function getUniqueDates(): Collection
    {
        return $this->groupBy(fn (TimeEntry $timeEntry) => $timeEntry->started_at->format('Y-m-d'))
            ->keys()
            ->unique()
            ->sort()
            ->values()
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
                    return sprintf('%s-%s/%s', $firstDate->format('d'), $lastDate->format('d'), $firstDate->format('m'));
                }

                return $firstDate->format('d/m') . ' - ' . $lastDate->format('d/m');
            })
            ->join(', ');
    }

    public function getDescription(): string
    {
        return sprintf('%s (%s)', $this->first()->project->name, $this->getDateString());
    }

    public function getRate(): ?float
    {
        /** @var \App\Models\TimeEntry $timeEntry */
        $timeEntry = $this->first();

        // TODO get/return default workspace rate as fallback
        return $timeEntry->project->rate;
    }
}
