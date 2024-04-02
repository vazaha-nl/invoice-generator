<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;

class ByProject extends TimeEntryRenderer
{
    protected string $description = 'By project name';

    public function render(TimeEntry $timeEntry): string
    {
        return $timeEntry->project->name;
    }
}
