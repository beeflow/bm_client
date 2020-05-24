<?php

declare(strict_types=1);

namespace BMClientBundle\Client\Services;

use BMClientBundle\Client\Services\Curl\Curl;
use BMClientBundle\Client\Services\Curl\CurlDto;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class BMApiRequest
{
    /**
     * @var string
     */
    private $apiUrl;

    public function __construct(string $apiUrl)
    {
        $this->apiUrl = $apiUrl;
    }

    public function request(string $method, string $url, array $params = []): ?array
    {
        $curlDto = new CurlDto();
        $curl = new Curl();

        $curlDto
            ->setUrl($this->apiUrl . $url)
            ->setData($params)
            ->setMethod($method);

        return $curl->send($curlDto);
    }
}
