<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;
use LogicException;

abstract class TimeEntryRenderer
{
    protected string $description;

    public function getDescription(): string
    {
        if (!isset($this->description)) {
            throw new LogicException('Please set the $description property');
        }

        return $this->description;
    }

    abstract public function render(TimeEntry $timeEntry): string;

}
