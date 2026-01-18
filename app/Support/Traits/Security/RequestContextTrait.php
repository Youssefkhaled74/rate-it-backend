<?php

namespace App\Support\Traits\Security;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

trait RequestContextTrait
{
    protected function requestId(): ?string
    {
        $id = request()->header('X-Request-Id') ?? request()->header('x-request-id');
        if ($id) {
            return (string) $id;
        }

        return Str::uuid()->toString();
    }

    protected function ip(): ?string
    {
        return request()->ip();
    }

    protected function userAgent(): ?string
    {
        return request()->userAgent();
    }
}
