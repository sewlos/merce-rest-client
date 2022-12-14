<?php

declare(strict_types = 1);

namespace Merce\RestClient\AuthTokenPlug\src\DTO\JWTToknen;

class TokenPayLoad
{

    public function __construct(
        public TokenPayloadUser $user,
        public ?int $expDate
    ) {
    }
}