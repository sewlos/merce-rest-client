<?php

declare(strict_types = 1);

namespace Merce\RestClient\Test\Unit\HttpPlug\Middleware;

use PHPUnit\Framework\TestCase;
use Merce\RestClient\HttpPlug\src\Support\EHttpMethod;
use Merce\RestClient\HttpPlug\src\Exception\Impl\InvalidArgumentException;
use Merce\RestClient\HttpPlug\src\Core\Builder\Request\Impl\RequestBuilder;
use Merce\RestClient\AuthTokenPlug\src\Core\TokenController\JWTToken\ManualJWTAuthTokenController;

//use Merce\RestClient\HttpPlug\src\Core\Middleware\Impl\AuthMiddleware;
//use Merce\RestClient\HttpPlug\src\Exception\Impl\InvalidArgumentException;

/**
 *  Test AuthMiddleware class
 */
class JWTAuthTest extends TestCase
{

    /**
     * "Expect" request header equal to token
     *
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testBearerAuthListener(): void
    {

        $authController = new ManualJWTAuthTokenController('merce.com.secret.token');
        $newRequest = $authController->authenticate((new RequestBuilder())->setUri('/test.login')->setMethod(EHttpMethod::GET)->getRequest());

        $this->assertEquals('Bearer merce.com.secret.token', $newRequest->getHeaderLine('Authorization'));
    }

    /**
     * Test throwing InvalidArgumentException when invalid access token has been provided
     *
     * @return void
     */
    public function testInvalidCredentials(): void
    {

        $this->expectException(InvalidArgumentException::class);

        $authController = new ManualJWTAuthTokenController('');
    }
}
