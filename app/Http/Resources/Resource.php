<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;

abstract class Resource extends JsonResource
{
    protected int $status = Response::HTTP_OK;

    /**
     * @param mixed $resource
     * @param int|null $status
     */
    public function __construct($resource = null, ?int $status = null)
    {
        if ($status) {
            $this->status = $status;
        }
        parent::__construct($resource);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->status);
    }
}
