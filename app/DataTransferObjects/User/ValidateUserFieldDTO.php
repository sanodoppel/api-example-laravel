<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="nickname", type="string"),
 *      @OA\Property(property="email", type="string"),
 * )
 */
class ValidateUserFieldDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $nickname;

    protected readonly ?string $email;

    /**
     * @return string|null
     */
    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

}
