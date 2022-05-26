<?php

namespace App\DataTransferObjects\User;

use App\DataTransferObjects\DataTransferObject;
use App\DataTransferObjects\DataTransferObjectTrait;

/**
 * @OA\Schema(
 *      @OA\Property(property="title", type="string"),
 *      @OA\Property(property="phone", type="string"),
 *      @OA\Property(property="description", type="string"),
 * )
 */
class AddCompaniesDTO implements DataTransferObject
{
    use DataTransferObjectTrait;

    protected readonly ?string $title;

    protected readonly ?string $phone;

    protected readonly ?string $description;

    /**
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
