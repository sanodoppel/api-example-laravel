<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="currentPassword", type="string"),
 *      @OA\Property(property="newPassword", type="string"),
 * )
 */
class ChangePasswordDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $currentPassword;

    protected readonly ?string $newPassword;

    /**
     * @return string|null
     */
    public function getCurrentPassword(): ?string
    {
        return $this->currentPassword;
    }

    /**
     * @return string|null
     */
    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }
}
