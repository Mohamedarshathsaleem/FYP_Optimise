<?php

declare(strict_types=1);

namespace Database\Seeders\Support;

/**
 * Resolves the password given to seeded demo accounts.
 *
 * Local/testing environments fall back to a well-known default so the
 * Playwright suite can log in. In production a password must be supplied
 * via SEED_USER_PASSWORD; otherwise user seeding is skipped, so a public
 * deployment never ships accounts with passwords published in this repo.
 */
final class SeedPassword
{
    public static function resolve(string $localDefault): ?string
    {
        $configured = config('app.seed_user_password');

        if (is_string($configured) && $configured !== '') {
            return $configured;
        }

        if (app()->environment('production')) {
            return null;
        }

        return $localDefault;
    }
}
