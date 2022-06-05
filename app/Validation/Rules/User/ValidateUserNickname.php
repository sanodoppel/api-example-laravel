<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class ValidateUserNickname extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['nickname' => "string[]"])]
    public static function rules(): array
    {
        return [
            'nickname' => ['required', 'alpha_dash' ,'unique:users', 'string'],
        ];
    }
}
