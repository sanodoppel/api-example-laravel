<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="array", @OA\Items(
 *         @OA\Property(property="accessToken", type="string"),
 *         @OA\Property(property="refreshToken", type="string"),
 *         @OA\Property(property="fingerprint", type="string"),
 *         @OA\Property(property="tokenType", type="string"),
 *         @OA\Property(property="accessTokenExpiredIn", type="integer"),
 *         @OA\Property(property="refreshTokenExpiredIn", type="integer")
 *     )),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class AuthResource extends Resource
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
            'accessToken' => $this->resource['accessToken'],
            'refreshToken' => $this->resource['refreshToken'],
            'fingerprint' => $this->resource['fingerprint'],
            'tokenType' => 'Bearer',
            'accessTokenExpiredIn' => $this->resource['accessTokenExpiredIn'],
            'refreshTokenExpiredIn' => $this->resource['refreshTokenExpiredIn'],
        ], $this->status);
    }
}
