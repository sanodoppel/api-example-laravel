<?php

namespace App\Services;

use App\DataTransferObjects\User\CreateUserDTO;
use App\DataTransferObjects\User\ForgetPasswordDTO;
use App\DataTransferObjects\User\ResetPasswordDTO;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class UserService
{
    /**
     * @param CreateUserDTO $createUserDTO
     * @return mixed
     */
    public function create(CreateUserDTO $createUserDTO)
    {
        $data = $createUserDTO->getDataForModel();
        $data['password'] = Hash::make($data['password']);

        return User::create($data);
    }

    /**
     * @param ForgetPasswordDTO $forgetPasswordDTO
     * @return string
     */
    public function forgetPassword(ForgetPasswordDTO $forgetPasswordDTO): string
    {
        return Password::sendResetLink(
            ['email' => $forgetPasswordDTO->getEmail()]
        );
    }

    /**
     * @param ResetPasswordDTO $resetPasswordDTO
     * @return string
     */
    public function resetPassword(ResetPasswordDTO $resetPasswordDTO): string
    {
        return Password::reset(
            [
                'email' => $resetPasswordDTO->getEmail(),
                'password' => $resetPasswordDTO->getPassword(),
                'token' => $resetPasswordDTO->getToken()
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ]);

                $user->save();
                event(new PasswordReset($user));
            }
        );
    }
}
