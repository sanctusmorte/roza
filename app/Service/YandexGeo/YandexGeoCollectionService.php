<?php

namespace App\Service\YandexGeo;

use App\Models\GeoLocation;
use App\Service\Yandex\Geo\Api;

/**
 * Class YandexGeoCollectionService
 * @package App\Service\YandexGeo
 */
class YandexGeoCollectionService
{
    const YANDEX_GEO_TOKEN = 'f5de97c5-850d-4e35-b3e2-e85aba272e55';

    public function getGeoCollectionForOrder(array $order): array
    {
        $geoQuery = $this->getGeoQuery($order);
        $order['geoQuery'] = $geoQuery;

        if ($geoQuery !== null) {
            $existGeoLocation  = GeoLocation::where('query', $geoQuery)->first();
            if ($existGeoLocation === null) {

                $geoData = $this->getGeoDataForQuery($geoQuery);

                if ($geoData['latitude'] !== null && $geoData['longitude'] !== null) {
                    $order['latitude'] = $geoData['latitude'];
                    $order['longitude'] = $geoData['longitude'];
                    $newGeoLocation = new GeoLocation();
                    $newGeoLocation->latitude = $geoData['latitude'];
                    $newGeoLocation->query = $geoQuery;
                    $newGeoLocation->longitude = $geoData['longitude'];
                    $newGeoLocation->save();
                } else {
                    $order['latitude'] = null;
                    $order['longitude'] = null;
                }

            } else {
                $order['latitude'] = $existGeoLocation->latitude;
                $order['longitude'] = $existGeoLocation->longitude;
            }
        } else {
            $order['latitude'] = null;
            $order['longitude'] = null;
        }

        return $order;
    }

    /**
     * @param $orders
     * @return int
     */
    public function getCountOrdersWithGeoLocation($orders): int
    {
        $count = 0;
        foreach ($orders as $order) {
            if ($order['latitude'] !== null && $order['longitude'] !== null) {
                $count++;
            }
        }
        return $count;
    }



    /**
     * @param $order
     * @return string
     */
    private function getGeoQuery($order)
    {
        $geoQuery = null;

        if (isset($order['delivery']['address']['city'])) {

            $geoQuery = $order['delivery']['address']['city'];

            if (isset($order['delivery']['address']['street'])) {
                $geoQuery = $geoQuery .  ', улица '  . $order['delivery']['address']['street'];
            } else if (isset($order['delivery']['address']['metro'])) {
                $geoQuery = $geoQuery .  ', м. '  . $order['delivery']['address']['metro'];
            }

            if (isset($order['delivery']['address']['house'])) {
                $geoQuery = $geoQuery .  ', стр. '  . $order['delivery']['address']['house'];
            }

            if (isset($order['delivery']['address']['flat'])) {
                $geoQuery = $geoQuery .  ', кв. '  . $order['delivery']['address']['flat'];
            }
        } else {
            if (isset($order['delivery']['address']['text'])) {
                $geoQuery = $order['delivery']['address']['text'];
            }
        }


        return $geoQuery;
    }

    private function getGeoDataForQuery(string $query): array
    {
        $data = [];
        $api = new Api();
        $api->setToken(self::YANDEX_GEO_TOKEN);
        $api->setQuery( $query);
        $api
            ->setLimit(1)// кол-во результатов
            ->setLang(Api::LANG_RU)// локаль ответа
            ->load();
        $response = $api->getResponse();
        $collection = $response->getList();

        $data['latitude'] = null;
        $data['longitude'] = null;

        if (count($collection) > 0) {
            foreach ($collection as  $item) {
                $data['latitude'] = $item->getLatitude(); // широта
                $data['longitude'] = $item->getLongitude(); // долгота
            }
        }

        return $data;
    }

}
