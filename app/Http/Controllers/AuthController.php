<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AuthDTO;
use App\DataTransferObjects\AuthRefreshTokenDTO;
use App\Http\Resources\AuthResource;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\FormErrorResource;
use App\Services\AuthService;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Rules\Auth;
use App\Validation\Rules\Fingerprint;
use App\Validation\Validation;
use Exception;
use Illuminate\Auth\AuthenticationException;
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
     * @throws Exception
     */
    #[Post('login', name: 'api_auth_login')]
    public function login(Request $request): JsonResource
    {
        $validation = new Validation(Auth::rules(), Auth::messages());
        $dto = new AuthDTO($request);

        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }
        $response = $this->authService->auth($dto);

        return $response ?
            new AuthResource($response) :
            new ErrorResource(ErrorEnum::UNAUTHORIZED->name, JsonResponse::HTTP_UNAUTHORIZED);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/refresh",
     *     description="Refresh token",
     *     tags={"Auth"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AuthRefreshTokenDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource")),
     *     security={{"auth_user":{}}}
     * )
     *
     * @param Request $request
     * @return JsonResource
     * @throws AuthenticationException
     */
    #[Post('refresh', name: 'api_auth_refresh', middleware: 'refresh.token')]
    public function refresh(Request $request): JsonResource
    {
        $dto = new AuthRefreshTokenDTO($request);
        $validation = new Validation(Fingerprint::rules(), Auth::messages());
        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }

        return new AuthResource(
            $this->authService->refresh($dto)
        );
    }

    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     description="Logout token",
     *     tags={"Auth"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/AuthRefreshTokenDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/EmptyResource"))
     * )
     *
     * @param Request $request
     * @return JsonResource
     * @throws AuthenticationException
     */
    #[Post('logout', name: 'api_auth_logout')]
    public function logout(Request $request): JsonResource
    {
        $dto = new AuthRefreshTokenDTO($request);
        $validation = new Validation(Fingerprint::rules(), Auth::messages());
        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }

        $this->authService->logout(new AuthRefreshTokenDTO($request));
        return new EmptyResource();
    }
}
