<?php

namespace App\Service\UserFilters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class UserFiltersService
 * @package App\Service\UserFilters
 */
class UserFiltersService
{
    const BASE_GROUP_STATUSES = ['new-group', 'approval-group', 'assembling-group', 'delivery-group'];
    const BASE_DELIVERY_TYPES = ['1'];

    /**
     * @param Request $requestData
     * @param array $userFilters
     * @return array
     */
    public function update(Request $requestData, array $userFilters): array
    {
       if ($requestData->input('inputFilterByGroupStatuses') !== null ) {
           if ($requestData->input('inputFilterByGroupStatuses') === 'all') {
               unset($userFilters['extendedStatus']);
           } else {
               $userFilters['extendedStatus'] = [$requestData->input('inputFilterByGroupStatuses')];
           }
       }

        return $userFilters;
    }

    public function getBaseGroupStatuses()
    {
        return self::BASE_GROUP_STATUSES;
    }


    public function getBaseFilters(array $userFilters): array
    {
        $filters = [];

        if (!isset($userFilters['extendedStatus'])) {
            $filters['extendedStatus'] = self::BASE_GROUP_STATUSES;
        } else {
            $filters['extendedStatus'] = $userFilters['extendedStatus'];
        }

        $filters['deliveryTypes'] = self::BASE_DELIVERY_TYPES;

        return $filters;
    }
}
