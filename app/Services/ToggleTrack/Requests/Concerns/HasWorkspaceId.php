<?php

namespace App\Services\ToggleTrack\Requests\Concerns;

trait HasWorkspaceId
{
    protected string $workspaceId;

    public function getWorkspaceId(): string
    {
        if (!isset($this->workspaceId)) {
            return config('toggl_track.workspace_id');
        }

        return $this->workspaceId;
    }
}
