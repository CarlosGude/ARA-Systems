<?php

namespace App\Tests\Api\Management\RoleGod;

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
    private $token;

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getToken();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllPurchaseLines(): void
    {
        $response = static::createClient()->request('GET', parent::API.'purchase_lines', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(13, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadPurchaseLine(): void
    {
        /** @var PurchaseLine $purchaseLine */
        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy(['product' => $this->getProduct()]);

        $response = static::createClient()->request(
            'GET',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token['token'],
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
        $company = $this->getCompany();
        $product = $this->getProduct();

        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company)->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

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
        $product = $this->getProduct();
        $purchaseLine = $this->getPurchaseLine($product);

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token['token'],
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
     * @throws TransportExceptionInterface
     */
    public function testDeleteAPurchase(): void
    {
        $product = $this->getProduct();
        $purchaseLine = $this->getPurchaseLine($product);

        static::createClient()->request('DELETE', parent::API.'purchase_lines/'.$purchaseLine->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
