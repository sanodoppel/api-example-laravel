<?php

namespace App\Models;

use App\Models\Boot\WithUuid;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use WithUuid;

    protected $table = 'files';

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
