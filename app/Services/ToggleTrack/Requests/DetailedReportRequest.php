<?php

namespace App\Services\ToggleTrack\Requests;

use InvalidArgumentException;

class DetailedReportRequest extends ReportRequest
{
    protected string $endpoint = '/reports/api/v2/details';

    protected int $page = 1;

    public function getQueryParams(): array
    {
        return array_merge([
            'page' => $this->page,
        ], parent::getQueryParams());
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function setPage(int $page): static
    {
        if ($page < 1) {
            throw new InvalidArgumentException('page cannot be smaller than 1');
        }

        $this->page = $page;

        return $this;
    }

    public function nextPage()
    {
        $this->page++;

        return $this;
    }

    public function previousPage()
    {
        if ($this->page > 1) {
            $this->page--;
        }

        return $this;
    }
}
