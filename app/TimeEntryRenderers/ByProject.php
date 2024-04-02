<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;

class ByProject extends TimeEntryRenderer
{
    protected string $description = 'Render lines by project name';

    public function render(TimeEntry $timeEntry): string
    {
        return $timeEntry->project->name;
    }
}
