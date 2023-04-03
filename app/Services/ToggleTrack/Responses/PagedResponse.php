<?php

namespace App\Services\ToggleTrack\Responses;

use App\Services\ToggleTrack\Requests\PagedRequest;

class PagedResponse extends Response
{
    public function hasNextPage(): bool
    {
        return $this->getNextRowNumber() !== null;
    }

    public function getNextPage(): ?Response
    {
        if (!$this->request instanceof PagedRequest) {
            return null;
        }

        return $this->apiClient->doRequest(
            $this->request->setFirstRowNumber($this->getNextRowNumber())
        );
    }

    public function getNextRowNumber(): ?string
    {
        return $this->httpResponse->getHeader('x-next-row-number')[0] ?? null;
    }
}
