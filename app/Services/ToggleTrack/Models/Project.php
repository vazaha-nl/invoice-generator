<?php

namespace App\Services\ToggleTrack\Models;

use App\Models\Project as EloquentProject;
use App\Models\Client as EloquentClient;
use App\Services\ToggleTrack\Models\Contracts\HasEloquentModel;

class Project extends Model implements HasEloquentModel
{
    public function getRate(): ?float
    {
        return $this->getProperty('rate');
    }

    public function isBillable(): bool
    {
        return $this->getRate() !== null;
    }

    public function getClientId(): ?int
    {
        return $this->getProperty('client_id');
    }

    public function getClient(): ?Client
    {
        if ($this->getClientId() === null) {
            return null;
        }

        return $this->apiClient->getClient($this->getClientId())->getModel();
    }

    public function toEloquentModel(): EloquentProject
    {
        $eloquentProject = EloquentProject::query()
            ->updateOrCreate(
                [
                    'toggl_id' => $this->getId(),
                ],
                [
                    'name' => $this->getName(),
                    'billable' => $this->isBillable(),
                    'rate' => $this->getRate(),
                ]
            );

        if ($this->getClientId() !== null) {
            $eloquentClient = EloquentClient::query()->where('toggl_id', $this->getClientId())->first();

            if ($eloquentClient === null) {
                $eloquentClient = $this->getClient()->toEloquentModel();
            }

            $eloquentProject->client()->associate($eloquentClient);
            $eloquentProject->save();
        }

        return $eloquentProject;
    }
}
