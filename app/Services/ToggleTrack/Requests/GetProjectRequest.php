<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\Project;
use App\Services\ToggleTrack\Requests\Concerns\HasWorkspaceId;

/**
 * @package App\Services\ToggleTrack\Requests
 * @see https://developers.track.toggl.com/docs/projects
 * */
class GetProjectRequest extends Request
{
    use HasWorkspaceId;

    protected string $modelClass = Project::class;

    public function __construct(
        protected int $projectId,
    ) {
        //
    }

    public function getEndpoint(): string
    {
        return sprintf('/api/v9/workspaces/%d/projects/%s', $this->getWorkspaceId(), $this->projectId);
    }
}
