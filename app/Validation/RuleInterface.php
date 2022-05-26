<?php

namespace App\Validation;

interface RuleInterface
{
    /**
     * @return array
     */
    public static function rules(): array;

    /**
     * @return array
     */
    public static function messages(): array;
}
