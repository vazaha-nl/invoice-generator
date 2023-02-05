<?php

namespace App\Services\EBoekhouden\Requests;

class GetRelationsRequest extends Request
{
    public function __construct(
        public ?string $keyword = null,
        public ?string $code = null,
        public int $id = 0,
    ) {
        //
    }

    public function toArray(): array
    {
        return [
            'cFilter' => [
                'Trefwoord' => $this->keyword,
                'Code' => $this->code,
                'ID' => $this->id,
            ],
        ];
    }

    public function getResultPath(): string
    {
        return 'GetRelatiesResult.Relaties.cRelatie';
    }

    public function getMethodName(): string
    {
        return 'GetRelaties';
    }
}
