<?php

namespace App\Http\Resources;

use App\Http\AppResponse;
use Illuminate\Http\Request;

class UserResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return AppResponse::prepare([
            'id' => $this->uuid,
            'name' => $this->name,
            'email' => $this->email,
            'nickname' => $this->nickname,
        ], $this->status);
    }
}
