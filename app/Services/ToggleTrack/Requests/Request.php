<?php

namespace App\Services\ToggleTrack\Requests;

abstract class Request
{
    protected string $method = 'GET';

    protected string $endpoint;

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getQueryParams(): array
    {
        return [];
    }
}
