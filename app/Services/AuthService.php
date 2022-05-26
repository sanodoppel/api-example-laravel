<?php

namespace App\Services;

use App\DataTransferObjects\AuthDTO;

class AuthService
{
    /**
     * @param AuthDTO $authDTO
     * @return string|null
     */
    public function auth(AuthDTO $authDTO): ?string
    {
        return auth()->attempt(['email' => $authDTO->getEmail(), 'password' => $authDTO->getPassword()]);
    }

    /**
     * @return string
     */
    public function refresh(): string
    {
        return auth()->refresh(true);
    }

    /**
     * @return void
     */
    public function logout(): void
    {
        auth()->logout(true);
    }
}
