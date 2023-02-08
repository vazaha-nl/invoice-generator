<?php

namespace App\Services\ToggleTrack;

use App\Services\ToggleTrack\Requests\GetClientsRequest;
use App\Services\ToggleTrack\Requests\GetProjectsRequest;
use App\Services\ToggleTrack\Requests\ReportRequest;
use App\Services\ToggleTrack\Requests\Request;

class ApiClient
{
    public function __construct(
        protected HttpClient $httpClient,
        protected string $userAgent,
        protected int $workspaceId,
    ) {
        //
    }

    public function getReport(ReportRequest $request)
    {
        return $this->doRequest($request);
    }

    protected function getDefaultQueryParams(): array
    {
        return [
            'user_agent' => config('toggl_track.user_agent'),
            'workspace_id' => config('toggl_track.workspace_id'),
        ];
    }

    public function doRequest(Request $request)
    {
        $response = $this->httpClient->request($request->getMethod(), $request->getEndpoint(), [
            'query' => array_merge($this->getDefaultQueryParams(), $request->getQueryParams()),
        ]);

        return json_decode($response->getBody()->getContents());
    }

    public function getClients()
    {
        return $this->doRequest(new GetClientsRequest($this->workspaceId));
    }

    public function getProjects()
    {
        return $this->doRequest(new GetProjectsRequest($this->workspaceId));
    }
}
