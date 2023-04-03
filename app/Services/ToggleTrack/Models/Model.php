<?php

namespace App\Services\ToggleTrack\Models;

use App\Services\ToggleTrack\ApiClient;

class Model
{
    public function __construct(
        protected array $data,
        protected ApiClient $apiClient,
    ) {
        //
    }

    public function getProperty(string $name, $default = null)
    {
        return $this->data[$name] ?? $default;
    }

    public function getId(): int
    {
        return $this->getProperty('id');
    }

    public function getName(): ?string
    {
        return $this->getProperty('name');
    }

    public function __toString()
    {
        return sprintf('%d - %s', $this->getId(), $this->getName());
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
