<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\Client;
use App\Services\ToggleTrack\Requests\Concerns\HasWorkspaceId;

class GetClientsRequest extends Request
{
    use HasWorkspaceId;

    protected string $modelClass = Client::class;

    public function getEndpoint(): string
    {
        return sprintf('/api/v9/workspaces/%d/clients', $this->getWorkspaceId());
    }
}
