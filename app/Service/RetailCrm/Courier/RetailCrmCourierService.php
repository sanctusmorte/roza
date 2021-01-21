<?php

namespace App\Service\RetailCrm\Courier;
use App\Models\Courier;
use App\Service\RetailCrm\RetailCrmApiClientService;
use Illuminate\Support\Facades\DB;


/**
 * Class RetailCrmCourierService
 * @package App\Service\RetailCrm
 */
class RetailCrmCourierService
{
    /**
     * @var \RetailCrm\ApiClient
     */
    private $retailCrmApiClient;

    private $existColorCodes;

    /**
     * RetailCrmCourierService constructor.
     * @param RetailCrmApiClientService $retailCrmApiClientService
     * @param RetailCrmCourierDataService $retailCrmCourierDataService
     */
    public function __construct(RetailCrmApiClientService $retailCrmApiClientService, RetailCrmCourierDataService $retailCrmCourierDataService)
    {
        $this->retailCrmApiClient = $retailCrmApiClientService->getApiClient();
        $this->existColorCodes = $retailCrmCourierDataService->getColorCodes();
    }

    /**
     * @return array
     */
    public function getCouriers(): array
    {
        $couriers = [];

        $response = $this->retailCrmApiClient->request->couriersList();
        if ($response->isSuccessful()) {
            foreach ($response->getResponse()['couriers'] as $courier) {
                if ($courier['active'] === true) {
                    $existCourier = Courier::where('exId', (string)$courier['id'])->first();
                    if ($existCourier === null) {
                        $newCourier = $this->setNewCourier($courier);
                        $couriers[] = $newCourier->toArray();
                    } else {
                        $couriers[] = $existCourier->toArray();
                    }
                }
            }

        }

        return $couriers;
    }

    public function setNewColorForExistCourier(array $requestData)
    {
        $response = [
            'error' => false,
            'msg' => '',
        ];

        $existCourier = Courier::find((int)$requestData['courierId']);
        if ($existCourier === null) {
            $response['error'] = true;
            $response['msg'] = 'Ошибка при обновлении цвета для курьера! Курьер с Id = '.$requestData['courierId'].' не найден.';
        } else {
            $existCourier->colorCode = $requestData['colorCode'];
            $existCourier->save();
            $response['msg'] = 'Цвет курьера #'.$requestData['courierId'].' успешно изменен!';
        }

        return $response;
    }

    private function setNewCourier(array $courier)
    {
        $newCourier = new Courier();

        $newCourier->exId = (string)$courier['id'];
        $newCourier->firstName = $courier['firstName'];
        if (isset($courier['lastName'])) {
            $newCourier->lastName = $courier['lastName'];
        }
        $newCourier->active = $courier['active'];
        if (isset($this->existColorCodes[$courier['id']])) {
            $newCourier->colorCode = $this->existColorCodes[$courier['id']];
        }
        $newCourier->save();

        return $newCourier;
    }


    /**
     * @param array $requestData
     * @param array $existOrders
     * @return array
     */
    public function setCourierForOrders(array $requestData, array $existOrders): array
    {
        $response = [
            'error' => false,
            'msg' => '',
        ];

        $countOfUpdatedOrders = 0;
        $countOfErrors = 0;

        if (isset($requestData['courier']) && isset($requestData['needOrders'])) {
            foreach ($requestData['needOrders'] as $needOrder) {
                $site = '';
                foreach ($existOrders as $existOrder) {
                    if ($existOrder['id'] === (int)$needOrder) {
                        $site = $existOrder['site'];
                    }
                }
                $orderData = [
                    'id' => (int)$needOrder,
                    'by' => 'id',
                    'site' => $site,
                    'delivery' => [
                        'code' => 'courier',
                        'data' => [
                            'courierId' => (int)$requestData['courier']
                        ],
                    ]
                ];

                $crmResponse = $this->retailCrmApiClient->request->ordersEdit($orderData, 'id', $site);
                if ($crmResponse->getResponse()['success'] === true) {
                    $countOfUpdatedOrders++;
                } else {
                    $countOfErrors++;
                }
            }

            if ($countOfErrors === 0) {
                $response['msg'] = 'Успешно обновлены '.$countOfUpdatedOrders.' заказов!';
            } else {
                $response['error'] = true;
                $response['msg'] = 'Произошла какая-то ошибка с обновлением заказов!';
            }

        } else {
            $response['error'] = true;
            $response['msg'] = 'Произошла AJAX ошибка - отсутствуют все значения!';
        }

        return $response;
    }
}
