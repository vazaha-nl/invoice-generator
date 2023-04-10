<?php

namespace App\Services\EBoekhouden\Responses;

use App\Services\EBoekhouden\Requests\Request;
use Illuminate\Support\Arr;
use stdClass;

class Response
{
    public function __construct(
        protected Request $request,
        protected stdClass $responseData,
    )
    {
        //
    }

    public function getResults()
    {
        if (!is_null($this->request->getResultPath())) {
            // TODO FIXME find some less hacky way to do this
            return Arr::get(
                json_decode(json_encode($this->responseData), true), $this->request->getResultPath()
            );
        }

        return $this->responseData;
    }

    public function getModels()
    {
        // TODO create models and instantiate
    }
}
