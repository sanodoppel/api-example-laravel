<?php

namespace App\Http\Controllers;

use App\DataTransferObjects\AuthDTO;
use App\DataTransferObjects\User\ChangePasswordDTO;
use App\DataTransferObjects\User\CreateUserDTO;
use App\DataTransferObjects\User\ForgetPasswordDTO;
use App\DataTransferObjects\User\ResetPasswordDTO;
use App\DataTransferObjects\User\UpdateUserDTO;
use App\DataTransferObjects\User\ValidateUserFieldDTO;
use App\Http\Resources\AuthResource;
use App\Http\Resources\EmptyResource;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ForgetPasswordResource;
use App\Http\Resources\FormErrorResource;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use App\Validation\Messages\ErrorEnum;
use App\Validation\Rules\Rule;
use App\Validation\Rules\User\ChangePassword;
use App\Validation\Rules\User\CreateUser;
use App\Validation\Rules\User\ForgetPassword;
use App\Validation\Rules\User\ResetPassword;
use App\Validation\Rules\User\UpdateUser;
use App\Validation\Rules\User\ValidateUserEmail;
use App\Validation\Rules\User\ValidateUserNickname;
use App\Validation\Rules\User\ValidateUserShortLink;
use App\Validation\Validation;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Password;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Post;
use Spatie\RouteAttributes\Attributes\Prefix;
use Spatie\RouteAttributes\Attributes\Put;
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
     * @throws Exception
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
                'email' => ['field' => ValidateUserEmail::rules()['email']],
                'nickname' => ['field' => ValidateUserNickname::rules()['nickname']],
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
    #[Post('password/forget', name: 'api_user_password_forget')]
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
     * @throws Exception
     */
    #[Post('password/reset', name: 'api_user_password_reset')]
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

    /**
     * @OA\Get(
     *     path="/api/user",
     *     description="Get current user",
     *     tags={"User"},
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UserResource")),
     *     security={{"auth_user":{}}}
     * )
     * @param Request $request
     * @return JsonResource
     */
    #[Get('', name: 'api_user_get')]
    public function get(Request $request): JsonResource
    {
        return new UserResource($this->getUser());
    }

    /**
     * @OA\Put(
     *     path="/api/user",
     *     description="Update current user",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/UpdateUserDTO")),
     *     @OA\Response(response=200, description="OK", @OA\JsonContent(ref="#/components/schemas/UserResource")),
     *     security={{"auth_user":{}}}
     * )
     * @param Request $request
     * @return JsonResource
     */
    #[Put('', name: 'api_user_update')]
    public function update(Request $request): JsonResource
    {
        $validation = new Validation(UpdateUser::rules(), Rule::messages());
        $dto = new UpdateUserDTO($request);

        return $validation->validate($dto) ?
            new UserResource($this->userService->update($this->getUser(), $dto)) :
            new FormErrorResource($validation->getErrors());
    }

    /**
     * @OA\Post(
     *     path="/api/user/password/change",
     *     description="Change restore",
     *     tags={"User"},
     *     @OA\RequestBody(@OA\JsonContent(ref="#/components/schemas/ChangePasswordDTO")),
     *     @OA\Response(
     *         response=200,
     *         description="OK",
     *         @OA\JsonContent(ref="#/components/schemas/AuthResource")
     *     ),
     *     security={{"auth_user":{}}}
     * )
     *
     * @param Request $request
     * @param AuthService $authService
     * @return JsonResource
     * @throws AuthenticationException|Exception
     */
    #[Post('password/change', name: 'api_user_password_change')]
    public function changePassword(Request $request, AuthService $authService): JsonResource
    {
        $validation = new Validation(ChangePassword::rules(), ChangePassword::messages());
        $dto = new ChangePasswordDTO($request);

        if (!$validation->validate($dto)) {
            return new FormErrorResource($validation->getErrors());
        }

        $user = $this->getUser();
        $this->userService->changePassword($user, $dto);

        $authDTO = new AuthDTO();
        $authDTO->setData(['email' => $user->getAttribute('email'), 'password' => $dto->getNewPassword()]);

        return new AuthResource($authService->auth($authDTO));
    }
}
