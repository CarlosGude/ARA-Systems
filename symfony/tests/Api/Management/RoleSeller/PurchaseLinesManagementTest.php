<?php

namespace App\Tests\Api\Management\RoleSeller;

use App\Entity\PurchaseLine;
use App\Tests\Api\BaseTest;
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
        $token = $this->getToken(parent::LOGIN_SELLER);
        static::createClient()->request('GET', parent::API.'purchase_lines', [
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
    public function testReadPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var PurchaseLine $purchaseLine */
        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy(['product' => $this->getProduct('Another Product', $this->getCompany('Another Company'))]);

        static::createClient()->request(
            'GET',
            parent::API.'purchase_lines/'.$purchaseLine->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer '.$token['token'],
                ],
            ]
        );

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $company = $this->getCompany('Another Company');
        $product = $this->getProduct('Another Product', $company);

        $purchase = [
            'purchase' => parent::API.'purchases/'.$this->getPurchase($company, 'pending')->getId(),
            'product' => parent::API.'products/'.$product->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'quantity' => 1,
        ];

        static::createClient()->request('POST', parent::API.'purchase_lines', [
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
    public function testEditAPurchaseLine(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $purchaseLine = $this->getPurchaseLine($this->getCompany(), 'pending');

        static::createClient()->request(
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
        $purchaseLine = $this->getPurchaseLine($this->getCompany(), 'pending');

        static::createClient()->request('DELETE', parent::API.'purchase_lines/'.$purchaseLine->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
