<?php

namespace App\Models\Boot;

use App\Helper\Uuid;

trait WithUuid
{

    public static function bootWithUuid()
    {
        self::creating(function ($model) {
            $model->uuid = Uuid::generate();
        });
    }
}
