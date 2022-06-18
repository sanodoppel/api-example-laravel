<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="field", type="string")
 * )
 */
class ValidateUserFieldDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $field;

    /**
     * @return string|null
     */
    public function getField(): ?string
    {
        return $this->field;
    }
}
