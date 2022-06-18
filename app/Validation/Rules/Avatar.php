<?php

namespace App\Validation\Rules;

use JetBrains\PhpStorm\ArrayShape;

abstract class Avatar
{
    /**
     * @return array
     */
    #[ArrayShape(['file' => "string[]"])]
    public static function rules(): array
    {
        return [
            'file' => ['required', 'image', 'max:2000'],
        ];
    }
}
