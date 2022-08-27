<?php

namespace App\Services\EBoekhouden\Models;

use Carbon\Carbon;

abstract class Model
{
    // abstract public function toArray(): array;

    public function formatDate(Carbon $date): string
    {
        return $date->format('Y-m-d');
    }
}
