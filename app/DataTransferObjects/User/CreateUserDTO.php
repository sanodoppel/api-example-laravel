<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="nickname", type="string"),
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="name", type="string"),
 *      @OA\Property(property="password", type="string"),
 * )
 */
class CreateUserDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $name;

    protected readonly ?string $nickname;

    protected readonly ?string $email;

    protected readonly ?string $password;


    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

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

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
