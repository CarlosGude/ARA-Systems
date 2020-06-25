<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PurchaseLineValidationTest extends BaseTest
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

    protected function getPurchaseData(): array
    {
        $company = $this->getCompany();
        $product = $this->getProduct();
        return [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company)->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPurchaseIsRequired(): void
    {
        $purchaseLine = $this->getPurchaseData();
        unset($purchaseLine['purchase']);

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($purchaseLine),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'purchase: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProductIsRequired(): void
    {
        $purchaseLine = $this->getPurchaseData();
        unset($purchaseLine['product']);

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchaseLine),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'product: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testQuantityGreaterThanOrEqualToOne(): void
    {
        $purchaseLine = $this->getPurchaseData();
        $purchaseLine['quantity'] = -1;

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchaseLine),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'quantity: Este valor debería ser mayor o igual que 1.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProductShouldNotBeUpdated(): void
    {
        $purchaseLine = $this->getPurchaseLine($this->getCompany(), 'pending');
        $newProduct = $this->getProduct('Product 1');

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$this->token['token'],
                    'Content-Type' => 'application/json',
                ],

                'body' => json_encode(['product' => parent::API.'products/'.$newProduct->getId()]),
            ]
        );

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals('product: This value should not be updated.', $response['hydra:description']);
    }
}
