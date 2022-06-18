<?php

namespace App\Http\Resources\Items;

use App\Models\File;
use App\Models\User;

/**
 * @OA\Schema(
 *     @OA\Property(property="id", type="string"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="email", type="string"),
 *     @OA\Property(property="nickname", type="string"),
 *     @OA\Property(property="timezone", type="string"),
 *     @OA\Property(property="avatar", type="object", ref="#/components/schemas/FileResourceItem")
 * )
 */
final class UserResourceItem
{
    public function __construct(private User $user)
    {
    }

    public function get(): array
    {
        $avatar = $file = File::where(['user_id' => $this->user->id, 'type' => 'avatar'])->first();

        return [
            'id' => $this->user->uuid,
            'name' => $this->user->name,
            'email' => $this->user->email,
            'nickname' => $this->user->nickname,
            'timezone' => $this->user->timezone,
            'avatar' => $avatar ? (new FileResourceItem($avatar))->get() : null
        ];
    }
}
