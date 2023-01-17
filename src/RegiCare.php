<?php

declare(strict_types=1);

namespace Southaxis\RegiCare;

use Graze\GuzzleHttp\JsonRpc\Client as RPCCLient;
use Southaxis\RegiCare\Client\Config;

abstract class RegiCare
{
    private RPCCLient $client;

    public function __construct(private Config $config)
    {
        $this->client = RPCCLient::factory(
            $this->getConfig()->getDomain(),
            [
                'timeout' => 100,
                'verify'  => false,
            ]
        );
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function getClient(): RPCCLient
    {
        return $this->client;
    }
}
