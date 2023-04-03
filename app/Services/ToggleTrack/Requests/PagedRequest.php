<?php

namespace App\Services\ToggleTrack\Requests;

abstract class PagedRequest extends Request
{
    protected int $firstRowNumber;

    public function setFirstRowNumber(int $firstRowNumber): self
    {
        $this->firstRowNumber = $firstRowNumber;

        return $this;
    }

    public function getBody(): array
    {
        return [
            'first_row_number' => $this->firstRowNumber ?? null,
        ];
    }
}
