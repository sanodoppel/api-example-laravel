<?php

namespace App\Helper;

class Uuid
{
    /**
     * @return string
     */
    public static function generate(): string
    {
        return (string) \Ramsey\Uuid\Uuid::uuid4();
    }
}
