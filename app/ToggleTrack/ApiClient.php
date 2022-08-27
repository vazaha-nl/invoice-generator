<?php

namespace App\ToggleTrack;

use GuzzleHttp\Client;

class ApiClient
{
    protected Client $httpClient;

    public function __construct()
    {
        $this->httpClient = new Client([
            'base_uri' => config('toggl.base_uri'),
            'auth' => [
                config('toggl.api_token'),
                'api_token',
            ]
        ]);
    }

    public function getDetailedReport(array $params = [])
    {
        return $this->request('GET', '/reports/api/v2/details', $params);
    }

    protected function getBaseParams(): array
    {
        return [
            'user_agent' => config('toggl.user_agent'),
            'workspace_id' => config('toggl.workspace_id'),
        ];
    }

    public function request(string $method, string $endpoint, array $queryParams = [])
    {
        $response = $this->httpClient->request($method, $endpoint, [
            'query' => array_merge($this->getBaseParams(), $queryParams),
        ]);

        return json_decode($response->getBody()->getContents());
    }
}
