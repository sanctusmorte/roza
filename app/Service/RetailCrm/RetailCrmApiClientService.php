<?php

namespace App\Service\RetailCrm;

use App\Service\Config\RetailCrmConfigService;
use Illuminate\Http\Request;

/**
 * Class RetailCrmApiClientService
 * @package App\Service\RetailCrm
 */
class RetailCrmApiClientService
{
    private $retailCrmConfigService;

    public function __construct(RetailCrmConfigService $retailCrmConfigService)
    {
        $this->retailCrmConfigService = $retailCrmConfigService;
    }

    /**
     * @param null $url
     * @param null $key
     * @return \RetailCrm\ApiClient
     */
    public function getApiClient($url = null, $key = null): \RetailCrm\ApiClient
    {
        if ($url === null && $key === null) {
            $config = $this->retailCrmConfigService->getConfig();
        } else {
            $config = [
                'url' => $url,
                'apiKey' => $key
            ];
        }

        return new \RetailCrm\ApiClient(
            $config['url'],
            $config['apiKey'],
            \RetailCrm\ApiClient::V5
        );
    }
}
