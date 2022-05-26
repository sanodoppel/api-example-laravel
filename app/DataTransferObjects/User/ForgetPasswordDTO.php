<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="email", type="string"),
 * )
 */
class ForgetPasswordDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $email;

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }
}
