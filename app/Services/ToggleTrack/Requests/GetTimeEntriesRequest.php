<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\TimeEntry;

class GetTimeEntriesRequest extends Request
{
    protected string $modelClass = TimeEntry::class;

    public function getEndpoint(): string
    {
        return '/api/v9/me/time_entries';
    }
}
