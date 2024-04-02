<?php

namespace App\TimeEntryRenderers;

use App\Models\TimeEntry;

class Quattro extends ByDescription
{
    public function render(TimeEntry $timeEntry): string
    {
        $project = $timeEntry->project;

        $prefix = '[MAINT] ';

        if (preg_match('/\[DEV\]/', $project->name)) {
            $prefix = '[DEV] ';
        }

        $description = parent::render($timeEntry);

        if (!preg_match('/trello/', $description)) {
            $description = 'overleg, overig';
        }


        return $prefix . $description;
    }
}
