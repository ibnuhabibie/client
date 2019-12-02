<?php

namespace Laracatch\Client\Client;

use Laracatch\Client\Client\Exceptions\BadResponseCode;
use Laracatch\Client\Client\Exceptions\InvalidData;
use Laracatch\Client\Client\Exceptions\NotFound;

class Client
{
    /** @var string */
    private $baseUrl;

    /** @var int */
    private $timeout;

    /**
     * Client constructor.
     *
     * @param string $baseUrl
     * @param int $timeout
     */
    public function __construct(string $baseUrl, int $timeout = 10)
    {
        $this->baseUrl = $baseUrl;
        $this->timeout = $timeout;
    }

    /**
     * @param string $url
     * @param array $arguments
     *
     * @return array|false
     * @throws BadResponseCode
     */
    public function get(string $url, array $arguments = [])
    {
        return $this->makeRequest('get', $url, $arguments);
    }

    /**
     * @param string $httpVerb
     * @param string $url
     * @param array $arguments
     *
     * @return mixed
     * @throws BadResponseCode
     */
    private function makeRequest(string $httpVerb, string $url, array $arguments = [])
    {
        $fullUrl = "{$this->baseUrl}/{$url}";

        $headers = [];

        $response = $this->makeCurlRequest($httpVerb, $fullUrl, $headers, $arguments);

        if ($response->getHttpResponseCode() === 422) {
            throw InvalidData::createForResponse($response);
        }

        if ($response->getHttpResponseCode() === 404) {
            throw NotFound::createForResponse($response);
        }

        if ($response->getHttpResponseCode() !== 200 && $response->getHttpResponseCode() !== 204) {
            throw BadResponseCode::createForResponse($response);
        }

        return $response->getBody();
    }

    public function makeCurlRequest(
        string $httpVerb,
        string $fullUrl,
        array $headers = [],
        array $arguments = []
    ): Response {
        if (! \extension_loaded('curl')) {
            throw new \LogicException('The "curl" extension is not installed.');
        }

        $curlHandle = $this->getCurlHandle($fullUrl, $headers);

        switch (strtolower($httpVerb)) {
            case 'get':
                curl_setopt($curlHandle, CURLOPT_URL, $fullUrl . '&' . http_build_query($arguments));
                break;

            case 'post':
                curl_setopt($curlHandle, CURLOPT_POST, true);
                $this->attachRequestPayload($curlHandle, $arguments);
                break;

            case 'put':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PUT');
                $this->attachRequestPayload($curlHandle, $arguments);
                break;

            case 'patch':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'PATCH');
                $this->attachRequestPayload($curlHandle, $arguments);
                break;

            case 'delete':
                curl_setopt($curlHandle, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
        }

        $body = json_decode(curl_exec($curlHandle), true);
        $headers = curl_getinfo($curlHandle);
        $error = curl_error($curlHandle);

        return new Response($headers, $body, $error);
    }

    /**
     * @param string $fullUrl
     * @param array $headers
     *
     * @return resource
     */
    private function getCurlHandle(string $fullUrl, array $headers = [])
    {
        $curlHandle = curl_init();

        curl_setopt($curlHandle, CURLOPT_URL, $fullUrl);

        curl_setopt($curlHandle, CURLOPT_HTTPHEADER, array_merge([
            'Accept: application/json',
            'Content-Type: application/json',
            'User-Agent: Laracatch/Client API 1.0'
        ], $headers));

        curl_setopt($curlHandle, CURLOPT_USERAGENT, 'Laracatch/Client API 1.0');
        curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandle, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curlHandle, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($curlHandle, CURLOPT_ENCODING, '');
        curl_setopt($curlHandle, CURLINFO_HEADER_OUT, true);

        return $curlHandle;
    }

    private function attachRequestPayload(&$curlHandle, array $data)
    {
        $encoded = json_encode($data);

        curl_setopt($curlHandle, CURLOPT_POSTFIELDS, $encoded);
    }

    /**
     * @param string $url
     * @param array $arguments
     *
     * @return array|false
     * @throws BadResponseCode
     */
    public function post(string $url, array $arguments = [])
    {
        return $this->makeRequest('post', $url, $arguments);
    }

    /**
     * @param string $url
     * @param array $arguments
     *
     * @return array|false
     * @throws BadResponseCode
     */
    public function patch(string $url, array $arguments = [])
    {
        return $this->makeRequest('patch', $url, $arguments);
    }

    /**
     * @param string $url
     * @param array $arguments
     *
     * @return array|false
     * @throws BadResponseCode
     */
    public function put(string $url, array $arguments = [])
    {
        return $this->makeRequest('put', $url, $arguments);
    }

    /**
     * @param string $method
     * @param array $arguments
     *
     * @return array|false
     * @throws BadResponseCode
     */
    public function delete(string $method, array $arguments = [])
    {
        return $this->makeRequest('delete', $method, $arguments);
    }
}
