<?php

namespace App\Services\EBoekhouden\Requests;

abstract class Request
{
    abstract public function toArray(): array;

    abstract public function getResultPath(): ?string;

    abstract public function getMethodName(): string;
}
