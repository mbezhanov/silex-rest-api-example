<?php

namespace App\Tests\Service;

use App\Exception\ApiProblemException;
use App\Service\JwtService;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;
use phpmock\MockBuilder;
use PHPUnit\Framework\TestCase;

class JwtServiceTest extends TestCase
{
    /**
     * @var JwtService
     */
    private $service;

    protected function setUp()
    {
        $this->service = new JwtService(new Builder(), new Parser(), new Sha256(), 'test');
    }

    public function testCreateToken()
    {
        $this->mockUniqid('App\\Service', 1);
        $this->mockTime('App\\Service', 123);
        $token = $this->service->createToken(1);
        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals($this->getJwtToken(), (string) $token);
    }

    public function testValidateTokenSuccess()
    {
        $this->mockUniqid('Lcobucci\\JWT', 1);
        $this->mockTime('Lcobucci\\JWT', 123);
        $isValid = $this->service->validateToken($this->getJwtToken());
        $this->assertTrue($isValid);
    }

    public function testValidateExpiredToken()
    {
        $this->mockUniqid('Lcobucci\\JWT', 1);
        $this->mockTime('Lcobucci\\JWT', 12345);
        $isValid = $this->service->validateToken($this->getJwtToken());
        $this->assertFalse($isValid);
    }

    public function testValidateInvalidToken()
    {
        $this->expectException(ApiProblemException::class);
        $this->service->validateToken('invalidtoken');
    }

    public function testRefreshToken()
    {
        $this->mockUniqid('App\\Service', 2);
        $this->mockTime('App\\Service', 234);
        $token = $this->service->refreshToken($this->getJwtToken());
        $this->assertInstanceOf(Token::class, $token);
        $this->assertEquals($this->getRefreshedJwtToken(), (string) $token);
    }

    private function mockUniqid($namespace, $value)
    {
        $builder = new MockBuilder();
        $builder->setNamespace($namespace)
            ->setName('uniqid')
            ->setFunction(
                function () use ($value) {
                    return $value;
                }
            );
        $mock = $builder->build();
        $mock->disable();
        $mock->enable();
    }

    private function mockTime($namespace, $value)
    {
        $builder = new MockBuilder();
        $builder->setNamespace($namespace)
            ->setName('time')
            ->setFunction(
                function () use ($value) {
                    return $value;
                }
            );
        $mock = $builder->build();
        $mock->disable();
        $mock->enable();
    }

    private function getJwtToken()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IjEifQ.eyJqdGkiOiIxIiwiaWF0IjoxMjMsIm5iZiI6MTIzLCJleHAiOjM3MjMsInVpZCI6MX0.LAgvnDqHPu7VUiX6MlmabcHSYBDJegkRthL9K-7p_To';
    }

    private function getRefreshedJwtToken()
    {
        return 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiIsImp0aSI6IjIifQ.eyJqdGkiOiIyIiwiaWF0IjoyMzQsIm5iZiI6MjM0LCJleHAiOjM4MzQsInVpZCI6MX0.Uz1nywExcppUgMWJJjQc4KGZwIJDX_Re_6O_H9tls_0';
    }
}
