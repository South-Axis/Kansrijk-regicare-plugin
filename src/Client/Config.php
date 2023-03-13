<?php

declare(strict_types=1);

namespace Southaxis\RegiCare\Client;

class Config
{
    public function __construct(
        private readonly string $domain,
        private readonly string $token
    )
    {
    }

    public function getDomain(): string
    {
        return $this->domain;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
