<?php

namespace App\Tests\Api\Management\RolePurchaser;

use App\Entity\Client;
use App\Tests\Api\BaseTest;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest.
 */
class ClientsManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllClients(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        static::createClient()->request('GET', parent::API.'clients', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAClient(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Client $client */
        $client = $this->getPurchaseClient();

        static::createClient()->request('GET', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnClient(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $client = [
            'name' => 'test',
            'email' => 'fake@email.com',
            'identification' => '3616884',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        static::createClient()->request('POST', parent::API.'clients', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($client),
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
    public function testEditAClient(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Client $client */
        $client = $this->getPurchaseClient('Another Client', $this->getCompany('Another Company'));

        static::createClient()->request('PUT', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Client']),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAClient(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Client $client */
        $client = $this->getPurchaseClient('Another Client', $this->getCompany('Another Company'));

        static::createClient()->request('DELETE', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
