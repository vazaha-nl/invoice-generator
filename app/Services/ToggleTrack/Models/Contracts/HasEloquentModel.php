<?php

namespace App\Services\ToggleTrack\Models\Contracts;

use Illuminate\Database\Eloquent\Model;

interface HasEloquentModel
{
    public function toEloquentModel(): Model;
}
