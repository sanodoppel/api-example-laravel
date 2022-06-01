<?php

namespace App\Validation\Rules;

use JetBrains\PhpStorm\ArrayShape;

abstract class Fingerprint  extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['fingerprint' => "string[]"])]
    public static function rules(): array
    {
        return [
            'fingerprint' => ['required', 'string', 'uuid'],
        ];
    }
}
