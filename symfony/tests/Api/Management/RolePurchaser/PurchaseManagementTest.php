<?php

namespace App\Tests\Api\Management\RolePurchaser;

use App\Entity\Purchase;
use App\Tests\Api\BaseTest;
use DateTime;
use DateTimeZone;
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $response = static::createClient()->request('GET', parent::API.'purchases', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(4, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        $response = static::createClient()->request('GET', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $purchase->getReference(),
            $response['reference']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $purchase = [
            'reference' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider('Another Provider')->getId(),
            'status' => 'pending',
        ];

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $purchase['reference'],
            $response['reference']
        );
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        $response = static::createClient()->request('PUT', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode([
                'reference' => 'test up',
            ]),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            'test up',
            $response['reference']
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAPurchase(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany('Another Company'), 'pending');

        static::createClient()->request('DELETE', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
