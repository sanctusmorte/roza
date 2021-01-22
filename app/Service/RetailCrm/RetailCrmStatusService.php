<?php

namespace App\Service\RetailCrm;
use App\Models\Courier;
use App\Service\RetailCrm\RetailCrmApiClientService;
use Illuminate\Support\Facades\DB;


/**
 * Class RetailCrmCourierService
 * @package App\Service\RetailCrm
 */
class RetailCrmStatusService
{
    /**
     * @var \RetailCrm\ApiClient
     */
    private $retailCrmApiClient;


    /**
     * RetailCrmStatusService constructor.
     * @param \App\Service\RetailCrm\RetailCrmApiClientService $retailCrmApiClientService
     */
    public function __construct(RetailCrmApiClientService $retailCrmApiClientService)
    {
        $this->retailCrmApiClient = $retailCrmApiClientService->getApiClient();

    }

    /**
     * @return array
     */
    public function getStatuses(): array
    {
        $statuses = [];

        $response = $this->retailCrmApiClient->request->statusesList();
        if ($response->isSuccessful()) {
            foreach ($response->getResponse()['statuses'] as $status) {
                if ($status['active'] === true) {
                   $statuses[] = $status;
                }
            }

        }

        return $statuses;
    }

    /**
     * @param array $groups
     * @return array
     */
    public function getStatusesByGroups(array $groups)
    {
        $statusesByGroups = [];
        $statuses = $this->getStatuses();

        foreach ($statuses as $status) {
            foreach ($groups as $group) {
                if ($status['group'] === substr($group['code'], 0, -6)) {
                    $statusesByGroups[] = $status;
                }
            }
        }

        return $statusesByGroups;
    }

}
