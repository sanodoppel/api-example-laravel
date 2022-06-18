<?php

namespace App\Validation\Rules\User;

use App\Validation\Rules\Rule;
use JetBrains\PhpStorm\ArrayShape;

abstract class ChangePassword extends Rule
{
    /**
     * @return array
     */
    #[ArrayShape(['currentPassword' => "string[]", 'newPassword' => "string[]"])]
    public static function rules(): array
    {
        return [
            'currentPassword' => Rule::PASSWORD_RULE,
            'newPassword' => Rule::PASSWORD_RULE
        ];
    }
}
