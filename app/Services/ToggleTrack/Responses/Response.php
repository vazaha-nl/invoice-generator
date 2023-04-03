<?php

namespace App\Services\ToggleTrack\Responses;

use App\Services\ToggleTrack\ApiClient;
use App\Services\ToggleTrack\Models\Model;
use App\Services\ToggleTrack\Requests\Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Illuminate\Support\Collection;

class Response
{
    protected Collection $models;

    public function __construct(
        protected ApiClient $apiClient,
        protected Request $request,
        protected Psr7Response $httpResponse,
    ) {
        //
    }

    /**
     * @return \Illuminate\Support\Collection|\App\Services\ToggleTrack\Models\Model[]
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function getModels(): Collection
    {
        if (!isset($this->models)) {
            $modelClass = $this->getModelClass();

            $this->models = Collection::make($this->getResults())
                ->map(fn ($modelData) => new $modelClass($modelData, $this->apiClient));
        }

        return $this->models;
    }

    public function getModel(): Model
    {
        return $this->getModels()->first();
    }

    public function getCount(): int
    {
        return $this->getModels()->count();
    }

    protected function getResults(): array
    {
        $decoded = json_decode($this->httpResponse->getBody()->getContents(), true);

        if ($decoded === false) {
            return [];
        }

        return array_is_list($decoded) ? $decoded : [$decoded];
    }

    protected function getModelClass(): ?string
    {
        return $this->request->getModelClass();
    }
}
