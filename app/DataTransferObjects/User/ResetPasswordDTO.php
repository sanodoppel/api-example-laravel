<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="password", type="string"),
 *      @OA\Property(property="token", type="string")
 * )
 */
class ResetPasswordDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $email;

    protected readonly ?string $password;

    protected readonly ?string $token;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @return string|null
     */
    public function getToken(): ?string
    {
        return $this->token;
    }
}
