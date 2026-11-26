<?php

declare(strict_types=1);

namespace Tests\Unit;

use Database\Seeders\Support\SeedPassword;
use Tests\TestCase;

class SeedPasswordTest extends TestCase
{
    public function test_configured_password_is_used_in_any_environment(): void
    {
        config(['app.seed_user_password' => 'S3cure!Demo']);
        $this->app['env'] = 'production';

        $this->assertSame('S3cure!Demo', SeedPassword::resolve('password'));
    }

    public function test_local_default_is_used_outside_production_when_not_configured(): void
    {
        config(['app.seed_user_password' => null]);
        $this->app['env'] = 'local';

        $this->assertSame('password', SeedPassword::resolve('password'));
    }

    public function test_returns_null_in_production_when_not_configured(): void
    {
        config(['app.seed_user_password' => '']);
        $this->app['env'] = 'production';

        $this->assertNull(SeedPassword::resolve('password'));
    }
}
