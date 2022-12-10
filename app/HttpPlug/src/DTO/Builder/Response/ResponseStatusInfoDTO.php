<?php

namespace Merce\RestClient\HttpPlug\src\DTO\Builder\Response;

class ResponseStatusInfoDTO
{
    public function __construct(
        public ?int $statusCode = null,
        public string $humanizedReponse = '',
        public ?string $protocolVersion = null,
    ) { }
}