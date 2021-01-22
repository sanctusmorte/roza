<?php

namespace App\Service\RetailCrm;

use App\Service\RetailCrm\Courier\RetailCrmCourierService;
use App\Service\UserFilters\UserFiltersService;
use App\Service\YandexGeo\YandexGeoCollectionService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Class RetailCrmOrderService
 * @package App\Providers\RetailCrm
 */
class RetailCrmDataService
{
    private $retailCrmOrderService, $retailCrmCourierService, $yandexGeoCollectionService;
    private $userFiltersService;

    /**
     * RetailCrmDataService constructor.
     * @param RetailCrmOrderService $retailCrmOrderService
     * @param RetailCrmCourierService $retailCrmCourierService
     * @param YandexGeoCollectionService $yandexGeoCollectionService
     * @param UserFiltersService $userFiltersService
     */
    public function __construct(RetailCrmOrderService $retailCrmOrderService, RetailCrmCourierService $retailCrmCourierService,
                                YandexGeoCollectionService $yandexGeoCollectionService, UserFiltersService $userFiltersService)
    {
        $this->retailCrmOrderService = $retailCrmOrderService;
        $this->retailCrmCourierService = $retailCrmCourierService;
        $this->yandexGeoCollectionService = $yandexGeoCollectionService;
        $this->userFiltersService = $userFiltersService;
    }

    public function init(array $userFilters)
    {
        $filters = $this->userFiltersService->getBaseFilters($userFilters);

        $orders = $this->retailCrmOrderService->getOrdersByFilters($filters);

        return [
            'orders' => $orders,
            'couriers' => $this->retailCrmCourierService->getCouriers(),
            'countOrdersWithGeoLocation' => $this->yandexGeoCollectionService->getCountOrdersWithGeoLocation($orders)
        ];
    }

}
