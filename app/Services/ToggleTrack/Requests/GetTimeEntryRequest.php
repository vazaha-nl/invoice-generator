<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\TimeEntry;

class GetTimeEntryRequest extends Request
{
    protected string $modelClass = TimeEntry::class;

    public function __construct(
        protected int $id,
    ) {
        //
    }

    public function getEndpoint(): string
    {
        return sprintf('/api/v9/me/time_entries/%d', $this->id);
    }
}
