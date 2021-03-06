<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use App\Http\Resources\Items\UserResourceItem;
use App\Models\File;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="object", ref="#/components/schemas/UserResourceItem"),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AppResponse::prepare((new UserResourceItem($this->resource))->get(), $this->status);
    }
}
