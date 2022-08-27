<?php

namespace App\ToggleTrack;

use Carbon\Carbon;
use DateInterval;

class Entry {
    public string $project;
    public string $client;
    public string $description;
    public Carbon $startedAt;
    public Carbon $endedAt;

    public function getDuration(): DateInterval
    {
        return $this->endedAt->diff($this->startedAt, true);
    }
}
