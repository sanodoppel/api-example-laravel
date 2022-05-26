<?php

namespace App\DataTransferObjects;

/**
 * @OA\Schema(
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="password", type="string"),
 * )
 */
class AuthDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $email;

    protected readonly ?string $password;

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
}
