<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AuthDTO;
use App\Http\Resources\AuthResource;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\FormErrorResource;
use App\Services\AuthService;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Rules\Auth;
use App\Validation\Validation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Illuminate\Http\Request;

#[Prefix('api/auth')]
class AuthController extends Controller
{
    use AuthorizesRequests;

    public function __construct(protected AuthService $authService)
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     description="User login",
     *     tags={"Auth"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AuthDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource"))
     * )
     *

     * @param Request $request
     * @return JsonResource
     */
    #[Post('login', name: 'api_auth_login')]
    public function login(Request $request): JsonResource
    {
        $validation = new Validation(Auth::rules(), Auth::messages());
        $dto = new AuthDTO($request);

        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }
        $token = $this->authService->auth($dto);

        return $token ?
            new AuthResource($token) :
            new ErrorResource(ErrorEnum::UNAUTHORIZED->name, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     description="Refresh token",
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource")),
     *     security={{"auth_user":{}}}
     * )
     *
     * @return JsonResource
     */
    #[Post('refresh', name: 'api_auth_refresh')]
    public function refresh(): JsonResource
    {
        return new AuthResource($this->authService->refresh());
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     description="Logout token",
     *     tags={"Auth"},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/EmptyResource")),
     *     security={{"auth_user":{}}}
     * )
     *
     * @return JsonResource
     */
    #[Post('logout', name: 'api_auth_logout')]
    public function logout(): JsonResource
    {
        $this->authService->logout();

        return new EmptyResource();
    }
}
