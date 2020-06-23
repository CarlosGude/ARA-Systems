<?php


namespace App\Tests\Api\Management\RoleSeller;


use App\Entity\ProductProvider;
use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductProviderManagementTest extends BaseTest
{

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllProductProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        static::createClient()->request('GET', parent::API.'product_providers', [
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
    public function testReadAProductProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        static::createClient()->request(
            'GET',
            parent::API.'product_providers/'.$productProvider->getId(),
            [
                'headers' => ['Authorization' => 'Bearer '.$token['token']]
            ]
        );

        self::assertResponseStatusCodeSame(400);
    }


    public function testAddAnProductToProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $productProvider = [
            'price' => 21,
            'product' => parent::API . 'products/' . $this->getProduct()->getId(),
            'provider' => parent::API . 'providers/' . $this->getProvider()->getId(),
        ];

        static::createClient()->request('POST', parent::API . 'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);

        self::assertResponseStatusCodeSame(400);
    }


    public function testEditAnProductToProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        static::createClient()->request(
            'PUT',
            parent::API . 'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['price' => 50]),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteAProductFormProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        static::createClient()->request(
            'DELETE',
            parent::API.'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

}