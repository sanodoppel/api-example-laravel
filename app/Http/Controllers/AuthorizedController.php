<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthorizedController extends Controller
{
    use AuthorizesRequests;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('auth:api');
    }
}
