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
    const BASE_GROUP_STATUSES = [
        0 => [
            'code' => 'new-group',
            'name' => 'Группа "Новый"'
        ],
        1 => [
            'code' => 'approval-group',
            'name' => 'Группа "Согласование"'
        ],
        2 => [
            'code' => 'assembling-group',
            'name' => 'Группа "Комплектация"'
        ],
        3 => [
            'code' => 'delivery-group',
            'name' => 'Группа "Доставка"'
        ],
    ];

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
               unset($userFilters['extendedStatus']);
               $userFilters['extendedStatus'][] = $requestData->input('inputFilterByGroupStatuses');
           }
       }

        if ($requestData->input('deliveryDateFrom') !== null ) {
            $deliveryDateFrom = $requestData->input('deliveryDateFrom');
            $deliveryDateFromFormatted = date('Y-m-d', strtotime($deliveryDateFrom));
            if ($deliveryDateFromFormatted !== '1970-01-01') {
                $userFilters['deliveryDateFrom'] = $deliveryDateFromFormatted;
            }
        } else {
            unset($userFilters['deliveryDateFrom']);
        }


        if ($requestData->input('deliveryDateTo') !== null ) {
            $deliveryDateTo = $requestData->input('deliveryDateTo');
            $deliveryDateToFormatted = date('Y-m-d', strtotime($deliveryDateTo));
            if ($deliveryDateToFormatted !== '1970-01-01') {
                $userFilters['deliveryDateTo'] = $deliveryDateToFormatted;
            }
        } else {
            unset($userFilters['deliveryDateTo']);
        }


        if ($requestData->input('selectedStatuses') !== null ) {
            $selectedStatusess = json_decode($requestData->input('selectedStatuses'), 1);
            unset($userFilters['extendedStatus']);
            foreach ($selectedStatusess as $item) {
                $userFilters['extendedStatus'][] = $item;
            }
        } else {
            unset($userFilters['extendedStatus']);
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
            foreach (self::BASE_GROUP_STATUSES as $BASE_GROUP_STATUS) {
                $filters['extendedStatus'][] = $BASE_GROUP_STATUS['code'];
            }
        } else {
            $filters['extendedStatus'] = $userFilters['extendedStatus'];
        }

        if (isset($userFilters['deliveryDateFrom'])) {
            $filters['deliveryDateFrom'] = $userFilters['deliveryDateFrom'];
        }

        if (isset($userFilters['deliveryDateTo'])) {
            $filters['deliveryDateTo'] = $userFilters['deliveryDateTo'];
        }

        $filters['deliveryTypes'] = self::BASE_DELIVERY_TYPES;

        return $filters;
    }
}
