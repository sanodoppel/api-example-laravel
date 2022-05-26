<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Messages\SuccessEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/**
 * @OA\Schema(
 *     @OA\Property(property="result", type="array", @OA\Items(
 *         @OA\Property(property="status", type="string"),
 *     )),
 *     @OA\Property(property="status", type="integer"),
 *     @OA\Property(property="meta", type="object")
 * )
 */
class ForgetPasswordResource extends Resource
{
    /**
     * @param  Request $request
     * @return array
     */
    public function toArray($request): array
    {
        return AppResponse::prepare([
            'status' => match ($this->resource) {
                Password::RESET_LINK_SENT => SuccessEnum::PASSWORD_SENT->name,
                Password::RESET_THROTTLED => ErrorEnum::THROTTLED->name,
                default => ErrorEnum::ERROR->name
            },
        ], $this->status);
    }
}
