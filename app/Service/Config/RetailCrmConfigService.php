<?php

namespace App\Service\Config;

/**
 * Class RetailCrmConfigService
 * @package App\Service\Config
 */
class RetailCrmConfigService
{
    const DEFAULT_CONFIG = [
        'url' => 'https://prostoroza.retailcrm.ru',
        'apiKey' => '0Br4vAQLwkiyiMmcOm5tnDkW64FTQ97A'
    ];
    /**
     * @return array
     */
    public function getConfig(): array
    {
        return self::DEFAULT_CONFIG;
    }
}
