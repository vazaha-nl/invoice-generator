<?php

namespace App\Services\ToggleTrack\Requests;

use App\Services\ToggleTrack\Models\Model;
use App\Services\ToggleTrack\Responses\PagedResponse;
use App\Services\ToggleTrack\Responses\Response;

abstract class Request
{
    protected string $method = 'GET';

    protected string $modelClass;

    protected string $responseClass;

    public function getMethod(): string
    {
        return $this->method;
    }

    abstract public function getEndpoint(): string;

    public function getQuery(): ?array
    {
        return [];
    }

    public function getBody(): ?array
    {
        return null;
    }

    public function getOptions(): array
    {
        return array_filter([
            'json' => $this->getBody(),
        ]);
    }

    public function getModelClass(): string
    {
        $default = Model::class;

        if (!isset($this->modelClass)) {
            return $default;
        }

        if (!is_subclass_of($this->modelClass, Model::class)) {
            return $default;
        }

        return $this->modelClass;
    }

    public function getResponseClass(): string
    {
        $default = $this instanceof PagedRequest ? PagedResponse::class : Response::class;

        if (!isset($this->responseClass)) {
            return $default;
        }

        if (!is_subclass_of($this->responseClass, Response::class)) {
            return $default;
        }

        return $this->responseClass;
    }
}
