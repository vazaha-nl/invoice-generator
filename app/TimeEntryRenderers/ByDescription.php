<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;

class ByDescription extends TimeEntryRenderer
{
    protected string $description = 'Render lines by description';

    public function render(TimeEntry $timeEntry): string
    {
        return $timeEntry->description;
    }
}
