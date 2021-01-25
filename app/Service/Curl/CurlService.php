<?php

namespace App\Service\Curl;

class CurlService
{
    /**
     * @var array
     */
    private $curlResponse;

    /**
     * CurlService constructor.
     */
    public function __construct()
    {
        $this->curlResponse = [
            'curl_errno' => 0,
            'curl_error' => '',
            'data' => '',
        ];
    }

    /**
     * @param string $curlUrl
     * @param array $curlHttpHeaders
     *
     * @return array
     */
    public function curlGet(string $curlUrl, array $curlHttpHeaders): array
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $curlUrl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHttpHeaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $this->curlResponse['data'] = curl_exec($ch);

        $this->curlResponse['curl_errno'] = curl_errno($ch);
        $this->curlResponse['curl_error'] = curl_error($ch);

        curl_close($ch);

        return $this->curlResponse;
    }
}
