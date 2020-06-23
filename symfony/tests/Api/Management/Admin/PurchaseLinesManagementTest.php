<?php

namespace App\Tests\Api\Management\Admin;

use App\Entity\PurchaseLine;
use App\Tests\Api\BaseTest;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PurchaseLinesManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllPurchaseLines(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $response = static::createClient()->request('GET', parent::API.'purchase_lines', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(1, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var PurchaseLine $purchaseLine */
        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy(['company' => $this->getCompany('Another Company')]);

        $response = static::createClient()->request(
            'GET',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token['token'],
                ],
            ]
        );

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $purchaseLine->getProduct()->getId(),
            $response['product']['id']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $company = $this->getCompany('Another Company');
        $product = $this->getProduct('Another Product', $company);

        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company, 'pending')->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $product->getId(),
            $response['product']['id']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy(['company' => $this->getCompany('Another Company')]);

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token['token'],
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['quantity' => 2]),
            ]
        );
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(2, $response['quantity']);
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);

        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy(['company' => $this->getCompany('Another Company')]);

        static::createClient()->request('DELETE', parent::API.'purchase_lines/'.$purchaseLine->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
