<?php

namespace App\Service\GrapHopper;

use App\Service\Curl\CurlService;

/**
 * Гео-кодер "graphhopper.com"
 * 500 беслпатных запросов в сутки
 * Запросы кэшируются в БД, что позволяет избежать повторный запрос на один и тот же адрес
 * Class GrapHopperGeoCoderService
 * @package App\Service\GrapHopper
 */
class GrapHopperGeoCoderService
{
    const API_KEY = '172a6916-bda1-4ee2-b204-25321087f3d8';
    const API_URL = 'https://graphhopper.com/api/1/geocode';

    private $curlService;

    public function __construct(CurlService $curlService)
    {
        $this->curlService = $curlService;
    }

    public function getGeoLocation(string $geoQuery)
    {
        $curlHttpHeaders = [
            'key' => self::API_KEY,
            'q' => $geoQuery,
            'locale' => 'ru'
        ];

        $geoLocation = [];

        $response = $this->curlService->curlGet(self::API_URL . '?' . http_build_query($curlHttpHeaders), []);

        $data = json_decode($response['data'], 1);
        if (isset($data['hits'])) {
            if (isset($data['hits'][0]['point'])) {
                $geoLocation['latitude'] = $data['hits'][0]['point']['lat'];
                $geoLocation['longitude'] = $data['hits'][0]['point']['lng'];
            }
        }

        return $geoLocation;
    }
}