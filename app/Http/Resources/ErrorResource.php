<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ErrorResource extends Resource
{
    protected int $status = Response::HTTP_INTERNAL_SERVER_ERROR;

    /**
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return AppResponse::prepare(
            $this->resource,
            $this->status,
        );
    }
}
