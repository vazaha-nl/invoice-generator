<?php

namespace App\Services\ToggleTrack\Requests;

class GetClientsRequest extends Request
{
    public function __construct(public int $workspaceId)
    {
        //
    }

    public function getEndpoint(): string
    {
        return sprintf('/api/v9/workspaces/%d/clients', $this->workspaceId);
    }

}
