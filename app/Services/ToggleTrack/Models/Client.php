<?php

namespace App\Services\ToggleTrack\Models;

use App\Models\Client as EloquentClient;
use App\Services\ToggleTrack\Models\Contracts\HasEloquentModel;

class Client extends Model implements HasEloquentModel
{
    public function toEloquentModel(): EloquentClient
    {
        return EloquentClient::query()
            ->updateOrCreate(
                [
                    'toggl_id' => $this->getId(),
                ],
                [
                    'name' => $this->getName(),
                ]
            );
    }
}
