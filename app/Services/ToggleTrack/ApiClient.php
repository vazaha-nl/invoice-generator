<?php

namespace App\Services\ToggleTrack;

use App\Services\ToggleTrack\Requests\GetClientRequest;
use App\Services\ToggleTrack\Requests\GetClientsRequest;
use App\Services\ToggleTrack\Requests\GetProjectRequest;
use App\Services\ToggleTrack\Requests\GetProjectsRequest;
use App\Services\ToggleTrack\Requests\GetTimeEntriesRequest;
use App\Services\ToggleTrack\Requests\GetTimeEntryRequest;
use App\Services\ToggleTrack\Requests\Request;
use App\Services\ToggleTrack\Requests\SearchTimeEntriesRequest;
use App\Services\ToggleTrack\Responses\PagedResponse;
use App\Services\ToggleTrack\Responses\Response;

class ApiClient
{
    public function __construct(
        protected HttpClient $httpClient,
        protected string $userAgent,
        protected string $workspaceId
    ) {
        //
    }

    public function doRequest(Request $request): Response
    {
        $response = $this->httpClient->request(
            $request->getMethod(),
            $request->getEndpoint(),
            $request->getOptions(),
        );

        $responseClass = $request->getResponseClass();

        return new $responseClass($this, $request, $response);
    }

    public function getTimeEntries(): Response
    {
        return $this->doRequest(new GetTimeEntriesRequest());
    }

    public function getTimeEntry(int $id): Response
    {
        return $this->doRequest(new GetTimeEntryRequest($id));
    }

    public function searchTimeEntries(
        array $clientIds,
        string|\DateTimeInterface $startDate,
        string|null|\DateTimeInterface $endDate = null,
    ): PagedResponse {
        return $this->doRequest(
            new SearchTimeEntriesRequest($clientIds, $startDate, $endDate)
        );
    }

    public function getClients(): Response
    {
        return $this->doRequest(new GetClientsRequest());
    }

    public function getClient(int $id): Response
    {
        return $this->doRequest(new GetClientRequest($id));
    }

    public function getProjects(): Response
    {
        return $this->doRequest(new GetProjectsRequest());
    }

    public function getProject(int $id): Response
    {
        return $this->doRequest(new GetProjectRequest($id));
    }
}
