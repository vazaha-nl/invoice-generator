<?php

namespace App\Services\EBoekhouden\Requests;

use App\Services\EBoekhouden\Models\Model;
use App\Services\EBoekhouden\Responses\Response;

abstract class Request
{
    protected string $modelClass;

    protected string $responseClass;

    abstract public function toArray(): array;

    abstract public function getResultPath(): ?string;

    abstract public function getMethodName(): string;

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
}
