<?php

namespace App\Services\ToggleTrack\Requests;

class GetClientsRequest extends Request
{
    public function getEndpoint(): string
    {
        // TODO FIXME this is wrong.
        return sprintf('/api/v9/workspaces/%d/clients', config('toggl_track.workspace_id'));
    }

}
