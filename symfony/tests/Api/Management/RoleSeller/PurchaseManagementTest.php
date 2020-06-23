<?php

namespace App\Tests\Api\Management\RoleSeller;

use App\Entity\Purchase;
use App\Tests\Api\BaseTest;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest.
 */
class PurchaseManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllPurchases(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        static::createClient()->request('GET', parent::API.'purchases', [
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
    public function testReadPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        static::createClient()->request('GET', parent::API.'purchases/'.$purchase->getId(), [
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
    public function testAddAPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $purchase = [
            'reference' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider('Another Provider')->getId(),
            'status' => 'pending',
        ];

        static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
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
    public function testEditAPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        static::createClient()->request('PUT', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode([
                'reference' => 'test up',
            ]),
        ]);
        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        static::createClient()->request('DELETE', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
