<?php

namespace App\Tests\Api\EventSubscriber\RolePurchaser;

use App\Entity\Purchase;
use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PurchaseLineEventSubscriberTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCantAddALineToPurchaseWithStatusIncoming(): void
    {
        $token = $this->getToken(self::LOGIN_PURCHASER);
        $company = $this->getCompany('The Company');
        $product = $this->getProduct('The Product', $company);

        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company, 'incoming')->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'purchase: A line can not be added to purchase to a purchase with status incoming.',
            $response['hydra:description']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testCantAddALineToPurchaseWithStatusCancelled(): void
    {
        $token = $this->getToken(self::LOGIN_PURCHASER);
        $company = $this->getCompany('The Company');
        $product = $this->getProduct('The Product', $company);

        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company, 'cancelled')->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        $response = static::createClient()->request('POST', parent::API.'purchase_lines', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'purchase: A line can not be added to purchase to a purchase with status cancelled.',
            $response['hydra:description']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testChangeStatusToSuccessAndUpdateStock(): void
    {
        $token = $this->getToken(self::LOGIN_PURCHASER);
        $company = $this->getCompany('The Company');
        $product = $this->getProduct('The Product', $company);
        $beforeStock = $product->getStockAct();

        self::assertEquals(100, $beforeStock);

        $purchase = $this->getPurchase();

        $response = static::createClient()->request(
            'PUT',
            parent::API.'purchases/'.$purchase->getId(),
            [
                'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode(['status' => Purchase::STATUS_SUCCESS]),
        ]
        );

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();

        $product = $this->getProduct();
        $afterStock = $product->getStockAct();

        $quantity = 0;
        foreach ($response['purchaseLines'] as $line) {
            if ($line['product']['@id'] === self::API.'products/'.$product->getId()) {
                $quantity = $line['quantity'];
                break;
            }
        }

        self::assertEquals($afterStock, $quantity + $beforeStock);
    }
}
