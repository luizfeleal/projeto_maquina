<?php

namespace App\Support;

class ApiMock
{
    public static function enabled(): bool
    {
        return (bool) config('mock.enabled', false);
    }

    public static function persist(): bool
    {
        return (bool) config('mock.persist', true);
    }

    public static function token(): string
    {
        return (string) config('mock.token', 'mock-api-token-dev');
    }
}
