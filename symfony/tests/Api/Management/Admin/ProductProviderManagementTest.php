<?php


namespace App\Tests\Api\Management\RoleAdmin;


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
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $response = static::createClient()->request('GET', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(10, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAProductProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider(
            'Another Product',
            'Another Provider',
            'Another Company'
        );

        $response = static::createClient()->request(
            'GET',
            parent::API.'product_providers/'.$productProvider->getId(),
            [
                'headers' => ['Authorization' => 'Bearer '.$token['token']]
            ]
        );

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();

        $this->assertEquals(
            parent::API.'products/'.$productProvider->getProduct()->getId(),
            $response['product']['@id']
        );

        $this->assertEquals(
            parent::API.'providers/'.$productProvider->getProvider()->getId(),
            $response['provider']['@id']
        );
    }


    public function testAddAnProductToProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $productProvider = [
            'price' => 21,
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        static::createClient()->request('POST', parent::API . 'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
    }


    public function testEditAnProductToProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider(
            'Another Product',
            'Another Provider',
            'Another Company'
        );

        $response = static::createClient()->request(
            'PUT',
            parent::API . 'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['price' => 50]),
        ]);
        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();

        $this->assertEquals(50, $response['price']);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteAProductFormProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider(
            'Another Product',
            'Another Provider',
            'Another Company'
        );

        static::createClient()->request(
            'DELETE',
            parent::API.'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
    }

}