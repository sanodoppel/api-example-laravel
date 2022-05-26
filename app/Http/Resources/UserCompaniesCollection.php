<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="array", @OA\Items(
 *         type="array",
 *         @OA\Items(
 *             @OA\Property(property="title", type="string"),
 *             @OA\Property(property="phone", type="string"),
               @OA\Property(property="description", type="string")
 *         )
 *     )),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class UserCompaniesCollection extends ResourceCollection
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AppResponse::prepare($this->collection->select(['title', 'phone', 'description'])->get());
    }
}
