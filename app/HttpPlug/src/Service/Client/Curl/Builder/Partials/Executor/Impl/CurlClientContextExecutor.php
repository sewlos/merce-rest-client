<?php

declare(strict_types = 1);

namespace Merce\RestClient\HttpPlug\src\Service\Client\Curl\Builder\Partials\Executor\Impl;

use CurlHandle;
use Psr\Http\Message\ResponseInterface;
use Merce\RestClient\HttpPlug\src\Exception\Impl\RequestException;
use Merce\RestClient\HttpPlug\src\DTO\Curl\Request\ICurlRequestPack;
use Merce\RestClient\HttpPlug\src\Core\Builder\Response\Impl\ResponseBuilder;
use Merce\RestClient\HttpPlug\src\Core\Client\Impl\Curl\Exception\CurlSSLException;
use Merce\RestClient\HttpPlug\src\Core\Client\Impl\Curl\Exception\CurlCallbackException;
use Merce\RestClient\HttpPlug\src\Service\Client\Curl\Builder\Partials\Executor\ACurlClientContextExecutor;

class CurlClientContextExecutor extends ACurlClientContextExecutor
{

    /**
     *
     * @var resource|CurlHandle|null
     */
    private $curl;

    public function __construct(
        ICurlRequestPack $curlGenericRequestBuilder
    ) {

        parent::__construct($curlGenericRequestBuilder);
        $this->curl = curl_init();
    }

    public function execute(): ResponseInterface
    {

        //init
        curl_setopt_array($this->curl, $this->curlGenericRequestBuilder->getData());

        //exec
        $response = curl_exec($this->curl);

        //error
        $this->parseError();

        //response
        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);

        $header = substr($response, 0, $headerSize);
        $body = substr($response, $headerSize);

        return (new ResponseBuilder())->parseHeaderLine($header)->setBody($body)->getResponse();
    }

    public function __destruct()
    {

        if (is_resource($this->curl)) {
            curl_reset($this->curl);
        }
    }

    /**
     * @return void
     */
    protected function parseError(): void
    {

        $errno = curl_errno($this->curl);

        $psrReuqest = $this->curlGenericRequestBuilder->getPSRRequest();

        switch ($errno) {
            case CURLE_OK:
                break;
            case CURLE_COULDNT_RESOLVE_PROXY:
            case CURLE_COULDNT_RESOLVE_HOST:
            case CURLE_COULDNT_CONNECT:
            case CURLE_OPERATION_TIMEOUTED:
            case CURLE_SSL_CONNECT_ERROR:
                throw new CurlSSLException($psrReuqest, curl_error($this->curl), $errno);
            case CURLE_ABORTED_BY_CALLBACK:
                throw new CurlCallbackException($psrReuqest, curl_error($this->curl), $errno);
            default:
                throw new RequestException($psrReuqest, curl_error($this->curl), $errno);
        }
    }
}