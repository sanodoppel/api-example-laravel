<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AuthDTO;
use App\DataTransferObjects\User\CreateUserDTO;
use App\DataTransferObjects\User\ForgetPasswordDTO;
use App\DataTransferObjects\User\ResetPasswordDTO;
use App\DataTransferObjects\User\ValidateUserFieldDTO;
use App\Http\Resources\AuthResource;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ForgetPasswordResource;
use App\Http\Resources\FormErrorResource;
use App\Services\AuthService;
use App\Services\UserService;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Rules\Rule;
use App\Validation\Rules\User\CreateUser;
use App\Validation\Rules\User\ForgetPassword;
use App\Validation\Rules\User\ResetPassword;
use App\Validation\Rules\User\ValidateUserEmail;
use App\Validation\Rules\User\ValidateUserNickname;
use App\Validation\Validation;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Where;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Prefix('api/user')]
#[Where('field', 'email|nickname')]
class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(private UserService $userService)
    {
        parent::__construct();
        $this->middleware('auth:api', ['except' => ['create', 'validateField', 'forgetPassword', 'resetPassword']]);
    }


    /**
     * @OA\Post(
     *     path="/api/user/create",
     *     description="Create new user",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/CreateUserDTO")),
     *     @OA\Response(response=201, description="OK", @OA\JsonContent(ref="#/components/schemas/AuthResource"))
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     */
    #[Post('create', name: 'api_user_create', middleware: 'api')]
    public function create(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(CreateUser::rules(), CreateUser::messages());
        $dto = new CreateUserDTO($request);

        if ($validation->validate($dto)) {
            $this->userService->create($dto);

            return new AuthResource($authService->auth(new AuthDTO($request)), JsonResponse::HTTP_CREATED);
        } else {
            return new FormErrorResource($validation->getErrors());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/user/validate/{field}",
     *     description="Validate user field. Values: email, nickname",
     *     tags={"User"},
     *     @OA\Parameter(
     *         name="field",
     *         in="path",
     *         description="email|nickname"
     *     ),
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ValidateUserFieldDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/EmptyResource"))
     * )
     *
     * @param Request $request
     * @param string $field
     * @return JsonResource
     */
    #[Post('validate/{field}', name: 'api_user_validate_field')]
    public function validateField(Request $request, string $field): JsonResource
    {
        $validation = new Validation(
            match ($field) {
                'email' => ValidateUserEmail::rules(),
                'nickname' => ValidateUserNickname::rules(),
            },
            Rule::messages()
        );

        $dto = new ValidateUserFieldDTO($request);
        return $validation->validate($dto) ?
            new EmptyResource() :
            new FormErrorResource($validation->getErrors());
    }

    /**
     * @OA\Post(
     *     path="/api/user/password/forget",
     *     description="Password forget link sending",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ForgetPasswordDTO")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ForgetPasswordResource")
     *     )
     * )
     *
     * @param Request $request
     *
     * @return JsonResource
     */
    #[Post('password/forget', name: 'api_auth_password_forget')]
    public function forgetPassword(Request $request): JsonResource
    {
        $validation = new Validation(ForgetPassword::rules(), Rule::messages());
        $dto = new ForgetPasswordDTO($request);

        return $validation->validate($dto) ?
            new ForgetPasswordResource($this->userService->forgetPassword($dto)) :
            new FormErrorResource($validation->getErrors());
    }

    /**
     * @OA\Post(
     *     path="/api/user/password/reset",
     *     description="Password restore",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ResetPasswordDTO")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/ForgetPasswordResource")
     *     )
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     */
    #[Post('password/reset', name: 'api_auth_password_reset')]
    public function resetPassword(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(ResetPassword::rules(), Rule::messages());
        $dto = new ResetPasswordDTO($request);

        if ($validation->validate($dto)) {
            return $this->userService->resetPassword($dto) === Password::PASSWORD_RESET ?
                new AuthResource($authService->auth(new AuthDTO($request))) :
                new ErrorResource(ErrorEnum::ERROR->name, JsonResponse::HTTP_BAD_REQUEST);
        } else {
            return new FormErrorResource($validation->getErrors());
        }
    }
}
