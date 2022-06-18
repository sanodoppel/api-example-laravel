<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use App\Http\Resources\Items\FileResourceItem;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="object", ref="#/components/schemas/FileResourceItem"),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class FileResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AppResponse::prepare((new FileResourceItem($this->resource))->get(), $this->status);
    }
}
