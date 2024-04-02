<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;

class ByDescription extends TimeEntryRenderer
{
    protected string $description = 'By description';

    public function render(TimeEntry $timeEntry): string
    {
        return $timeEntry->description;
    }
}
