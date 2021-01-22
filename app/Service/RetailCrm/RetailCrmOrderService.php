<?php

namespace App\Service\RetailCrm;

use App\Models\Courier;
use App\Service\RetailCrm\Courier\RetailCrmCourierService;
use App\Service\YandexGeo\YandexGeoCollectionService;

/**
 * Class RetailCrmOrderService
 * @package App\Providers\RetailCrm
 */
class RetailCrmOrderService
{
    private $retailCrmApiClient, $retailCrmCourierService, $yandexGeoCollectionService;

    const LIMIT = 20;
    const PAGE = 1;

    const BASE_ORDER_PARAMETERS = ['id', 'number', 'delivery', 'items', 'firstName', 'site', 'createdAt', 'customer', 'status'];

    private $statuses;

    /**
     * RetailCrmOrderService constructor.
     * @param RetailCrmApiClientService $retailCrmApiClientService
     * @param RetailCrmCourierService $retailCrmCourierService
     * @param YandexGeoCollectionService $yandexGeoCollectionService
     */
    public function __construct(RetailCrmApiClientService $retailCrmApiClientService, RetailCrmCourierService $retailCrmCourierService,
                                YandexGeoCollectionService $yandexGeoCollectionService, RetailCrmStatusService $retailCrmStatusService)
    {
        $this->retailCrmApiClient = $retailCrmApiClientService->getApiClient();
        $this->retailCrmCourierService = $retailCrmCourierService;
        $this->yandexGeoCollectionService = $yandexGeoCollectionService;
        $this->statuses = $retailCrmStatusService->getStatuses();
    }

    /**
     * @param array $filters
     * @return array
     */
    public function getOrdersByFilters(array $filters): array
    {
        $orders = [];
        $finalOrders = [];

        $response = $this->retailCrmApiClient->request->ordersList($filters, self::PAGE, self::LIMIT);

        if ($response->isSuccessful()) {
            if ($response->getResponse()['pagination']['totalCount'] > self::LIMIT) {
                for ($i = 1; $i <= $response->getResponse()['pagination']['totalPageCount']; $i++) {
                    $response = $this->retailCrmApiClient->request->ordersList($filters, $i, self::LIMIT);
                    $orders[] = $response->getResponse()['orders'];
                }
            } else {
                $orders[] = $response->getResponse()['orders'];
            }

            foreach ($orders as $data) {
                foreach ($data as $item) {
                    $finalOrders[] = $this->prepareOrderData($item);
                }
            }
        }



        return $finalOrders;
    }

    /**
     * @param string $statusCode
     * @return string
     */
    private function getStatusForOrder(string $statusCode)
    {
        $statusName = '';


        foreach ($this->statuses as $status) {
            if ($statusCode === $status['code']) {
                $statusName = $status['name'];
            }
        }

        return $statusName;
    }

    /**
     * @param array $item
     * @return array
     */
    public function prepareOrderData(array $item)
    {
        $needData = [];

        foreach (self::BASE_ORDER_PARAMETERS as $param) {
            if (isset($item[$param])) {
                $needData[$param] = $item[$param];
            } else {
                $needData[$param] = '';
            }
        }

        $needData['status'] = $this->getStatusForOrder($needData['status']);

        $needData = $this->yandexGeoCollectionService->getGeoCollectionForOrder($needData);
        $needData = $this->getColorCodeForCourier($needData);

        $deliveryDate = '';
        if (isset($item['delivery']['date'])) {
            $deliveryDate = $item['delivery']['date'];
        }

        $deliveryTime = '';
        if (isset($item['delivery']['time']['from']) && isset($item['delivery']['time']['to'])) {
            $deliveryTime = $item['delivery']['time']['from'] . ' - ' . $item['delivery']['time']['to'];
        }

        $needData['deliveryDate'] = $deliveryDate;
        $needData['deliveryTime'] = $deliveryTime;

        return $needData;
    }

    /**
     * @param array $needData
     * @return array
     */
    private function getColorCodeForCourier(array $needData): array
    {
        $iconColor = '#369fc9';

        if (isset($needData['delivery']['data']['id'])) {
            $existCourier = Courier::where('exId', (string)$needData['delivery']['data']['id'])->first();
            if ($existCourier !== null) {
                $iconColor = $existCourier->colorCode;
                $needData['courierName'] = $existCourier->firstName . ' ' . $existCourier->lastName;
                $needData['isCourierSelected'] = true;
            } else {
                $needData['courierName'] = 'Курьер с ID = '.$needData['delivery']['data']['id'].'';
                $needData['isCourierSelected'] = true;
            }
        } else {
            if ($needData['status'] === 'assembling-complete') {
                $iconColor = '#808080';
            } else if (
                $needData['status'] === 'new' ||
                $needData['status'] === 'availability-confirmed' ||
                $needData['status'] === 'offer-analog' ||
                $needData['status'] === 'prepayed' ||
                $needData['status'] === 'send-to-assembling' ||
                $needData['status'] === 'assembling'
            ) {
                $iconColor = '#ffffff';
            }
            $needData['courierName'] = '';
            $needData['isCourierSelected'] = false;
        }

        $needData['iconColor'] = $iconColor;

        return $needData;
    }


    /**
     * @param array $requestData
     * @return array
     */
    public function updateExistOrderByOrderId(array $requestData): array
    {
        $response = [
            'error' => false,
            'msg' => '',
            'updatedOrder' => null,
        ];

        $orderId = null;
        $courier = null;
        $site = null;

        if (isset($requestData['orderId']) &&
            isset($requestData['courier']) &&
            isset($requestData['site'])
        ) {
            $orderId = (int)$requestData['orderId'];
            if (isset($requestData['courier'])) {
                $courier = $requestData['courier'];
            }
            $site = $requestData['site'];
        } else {
            $response['error'] = true;
            $response['msg'] = 'Произошла AJAX ошибка - отсутствуют все значения!';
        }

        if ($response['error'] === false) {
            $order = [
                'id' => $orderId,
                'by' => 'id',
                'site' => $site,
                'delivery' => [
                    'code' => '1',
                    'data' => [
                        'courierId' => $courier
                    ],
                ]
            ];

            $crmResponse = $this->retailCrmApiClient->request->ordersEdit($order, 'id', $site);
            if ($crmResponse->getResponse()['success'] === false) {
                $response['error'] = true;
                $response['msg'] = $crmResponse->getResponse();
            } else {
                $response['msg'] = 'Заказ '.$orderId.' успешно обновлен! Курьер теперь - '.$courier.'';
                $updatedOrder = $crmResponse->getResponse()['order'];
                $needData = $this->prepareOrderData($updatedOrder);
                $needData['status'] = $this->getStatusForOrder($needData['status']);
                $response['updatedOrder'] = $updatedOrder;
                $response['updatedOrder']['iconColor'] = $needData['iconColor'];
                $response['updatedOrder']['deliveryDate'] = $needData['deliveryDate'];
                $response['updatedOrder']['deliveryTime'] = $needData['deliveryTime'];
            }
        }

        return $response;
    }
}
