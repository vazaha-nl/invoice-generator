<?php

namespace App\Services\ToggleTrack\Models;

class TimeEntry extends Model
{
    public function getName(): string
    {
        return $this->getProperty('description');
    }
}
