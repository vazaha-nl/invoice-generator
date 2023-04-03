<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\Client;
use App\Services\ToggleTrack\Requests\Concerns\HasWorkspaceId;

class GetClientRequest extends Request
{
    use HasWorkspaceId;

    protected string $modelClass = Client::class;

    public function __construct(
        protected int $clientId
    ) {
        //
    }

    public function getEndpoint(): string
    {
        return sprintf('/api/v9/workspaces/%d/clients/%d', $this->getWorkspaceId(), $this->clientId);
    }
}
