<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Swagger documentation",
 * )
 *
 */
class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware('api');
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return auth()->user();
    }
}
