<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="firstName", type="string"),
 *      @OA\Property(property="lastName", type="string"),
 *      @OA\Property(property="email", type="string"),
 *      @OA\Property(property="phone", type="string"),
 *      @OA\Property(property="password", type="string"),
 * )
 */
class CreateUserDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $firstName;

    protected readonly ?string $lastName;

    protected readonly ?string $email;

    protected readonly ?string $phone;

    protected readonly ?string $password;

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
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
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }
}
