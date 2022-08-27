<?php

namespace App\Services\ToggleTrack;

use GuzzleHttp\Client;

class HttpClient extends Client
{
    public function __construct(
        protected string $baseUri,
        protected string $apiToken,
    ) {

        parent::__construct([
            'base_uri' => $baseUri,
            'auth' => [$apiToken, 'api_token'],
        ]);
    }
}
