<?php

namespace App\Console\Commands;

use App\Services\AuthService;
use Illuminate\Console\Command;

class RemoveExpiredTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'remove:expired-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired refresh tokens';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(AuthService $authService)
    {
        $authService->removeExpired();

        return 0;
    }
}
