<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="array", @OA\Items(
 *         @OA\Property(property="id", type="string"),
 *         @OA\Property(property="name", type="string"),
 *         @OA\Property(property="email", type="string"),
 *         @OA\Property(property="nickname", type="string")
 *     )),
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
        return AppResponse::prepare([
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'nickname' => $this->nickname,
        ], $this->status);
    }
}
