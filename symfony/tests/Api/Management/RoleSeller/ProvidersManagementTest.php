<?php

namespace App\Tests\Api\Management\RoleSeller;

use App\Entity\Provider;
use App\Tests\Api\BaseTest;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest.
 */
class ProvidersManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllProviders(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        static::createClient()->request('GET', parent::API.'providers', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Provider $provider */
        $provider = $this->getProvider();

        static::createClient()->request('GET', parent::API.'providers/'.$provider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $provider = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@gmail.com',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        static::createClient()->request('POST', parent::API.'providers', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($provider),
        ]);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Provider $provider */
        $provider = $this->getProvider('Another Provider', $this->getCompany('Another Provider'));

        static::createClient()->request('PUT', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode(['name' => 'Fake Provider']),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Provider $provider */
        $provider = $this->getProvider('Another Provider', $this->getCompany('Another Provider'));

        static::createClient()->request('DELETE', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
