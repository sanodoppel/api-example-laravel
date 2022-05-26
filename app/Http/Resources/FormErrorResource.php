<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use App\Validation\Messages\ErrorEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class FormErrorResource extends ErrorResource
{
    protected int $status = Response::HTTP_BAD_REQUEST;

    /**
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return AppResponse::prepare(
            ErrorEnum::FORM_ERROR,
            $this->status,
            $this->resource
        );
    }
}
