<?php

namespace App\Tests\Api\PurchaseLine;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class EventSubscriberTest.
 */
class EventSubscriberTest extends BaseTest
{
    /**
     * @var array
     */
    protected $token;

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
    public function testAddLineAndUpdateTotal(): void
    {
        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase()->getId(),
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getProduct()->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();

        $response = json_decode($response->getContent(), true);

        $this->assertEquals(100, $response['price']);
        $this->assertEquals(101, $response['quantity']);
        $this->assertEquals(21, $response['tax']);
        $this->assertEquals(10100, $response['purchase']['total']);
        $this->assertEquals(2121, $response['purchase']['taxes']);
    }

    public function testRemoveLineAndUpdateTotal(): void
    {
        $purchase = $this->getPurchase($this->getCompany(), 'm_l_pur');
        $this->refresh($purchase);

        $this->assertEquals(10, $purchase->getPurchaseLines()->count());
        $this->assertEquals(1000, $purchase->getTotal());

        $this->remove($purchase->getPurchaseLines()->first());

        $this->assertEquals(9, $purchase->getPurchaseLines()->count());
        $this->assertEquals(900, $purchase->getTotal());
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddQuantityToLineAndUpdateTotal(): void
    {
        $product = $this->getProduct('Another Product', $this->getCompany('Another Company'));
        $purchaseLine = $this->getPurchaseLine($product, 'm_q_pur');

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token['token'],
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['quantity' => 20]),
            ]
        );
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(100, $response['price']);
        $this->assertEquals(20, $response['quantity']);
        $this->assertEquals(21, $response['tax']);
        $this->assertEquals(2000, $response['purchase']['total']);
        $this->assertEquals(420, $response['purchase']['taxes']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testRemoveQuantityToLineAndUpdateTotal(): void
    {
        $product = $this->getProduct('Another Product', $this->getCompany('Another Company'));
        $purchaseLine = $this->getPurchaseLine($product, 'm_q_pur');

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token['token'],
                    'Content-Type' => 'application/json',
                ],
                'body' => json_encode(['quantity' => 5]),
            ]
        );
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(100, $response['price']);
        $this->assertEquals(5, $response['quantity']);
        $this->assertEquals(21, $response['tax']);
        $this->assertEquals(500, $response['purchase']['total']);
        $this->assertEquals(105, $response['purchase']['taxes']);
    }
}
