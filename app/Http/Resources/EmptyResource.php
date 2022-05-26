<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;
use stdClass;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="object"),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class EmptyResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AppResponse::prepare(new stdClass(), $this->status);
    }
}
