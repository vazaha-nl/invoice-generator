<?php

namespace App\Services\ToggleTrack\Models;

use App\Models\Project as EloquentProject;
use App\Models\TimeEntry as EloquentTimeEntry;
use App\Services\ToggleTrack\Models\Contracts\HasEloquentModel;
use Carbon\Carbon;
use LogicException;

class GroupedTimeEntry extends Model implements HasEloquentModel
{
    public function getId(): int
    {
        $timeEntries = $this->getProperty('time_entries');

        return $timeEntries[0]['id'];
    }

    public function getTimeEntryData(): ?array
    {
        return $this->getProperty('time_entries')[0];
    }

    public function getStart(): Carbon
    {
        return Carbon::parse($this->getTimeEntryData()['start']);
    }

    public function getStop(): Carbon
    {
        return Carbon::parse($this->getTimeEntryData()['stop']);
    }

    public function getTimeEntries()
    {
        // it seems impossible to get non grouped time entries by id?
        throw new LogicException('Not implemented');
    }

    public function getProjectId(): ?int
    {
        return $this->getProperty('project_id');
    }

    public function getProject(): ?Project
    {
        // TODO refactor, make more efficient so it does not give rate limit errors
        sleep(1);
        $projectId = $this->getProjectId();

        if ($projectId === null) {
            return null;
        }

        return $this->apiClient->getProject($projectId)->getModel();
    }

    public function getClientId(): ?int
    {
        return $this->getProperty('client_id');
    }

    public function getClient(): ?Client
    {
        $clientId = $this->getClientId();

        if ($clientId === null) {
            return null;
        }

        return $this->apiClient->getClient($clientId)->getModel();
    }

    public function getDescription(): string
    {
        return $this->getProperty('description');
    }

    public function isBillable(): bool
    {
        return $this->getProperty('billable', false);
    }

    public function getHourlyRateInCents(): ?int
    {
        return $this->getProperty('hourly_rate_in_cents');
    }

    public function getCurrency(): ?string
    {
        return $this->getProperty('currency');
    }

    public function toEloquentModel(): EloquentTimeEntry
    {
        $eloquentTimeEntry = EloquentTimeEntry::query()
            ->updateOrCreate(
                [
                    'toggl_id' => $this->getId(),
                ],
                [
                    'description' => $this->getDescription(),
                    'started_at' => $this->getStart(),
                    'stopped_at' => $this->getStop(),
                ]
            );

        if ($this->getProjectId() !== null) {
            // $eloquentProject = EloquentProject::query()->where('toggl_id', $this->getProjectId())->first();

            // if ($eloquentProject === null) {
            //     $eloquentProject = $this->getProject()->toEloquentModel();
            // }
            $eloquentProject = $this->getProject()->toEloquentModel();

            $eloquentTimeEntry->project()->associate($eloquentProject);
            $eloquentTimeEntry->save();
        }

        return $eloquentTimeEntry;
    }

}
