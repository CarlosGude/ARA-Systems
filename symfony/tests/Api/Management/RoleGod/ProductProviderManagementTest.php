<?php


namespace App\Tests\Api\Management\RoleGod;


use App\Entity\ProductProvider;
use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductProviderManagementTest extends BaseTest
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
    public function testReadAllProductProvider(): void
    {
        $response = static::createClient()->request('GET', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(20, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAProductProvider(): void
    {
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        $response = static::createClient()->request(
            'GET',
            parent::API.'product_providers/'.$productProvider->getId(),
            [
                'headers' => ['Authorization' => 'Bearer '.$this->token['token']]
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
        $productProvider = [
            'price' => 21,
            'product' => parent::API . 'products/' . $this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API . 'providers/' . $this->getProvider()->getId(),
        ];

        static::createClient()->request('POST', parent::API . 'product_providers', [
            'headers' => ['Authorization' => 'Bearer ' . $this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
    }


    public function testEditAnProductToProvider(): void
    {
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        $response = static::createClient()->request(
            'PUT',
            parent::API . 'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer ' . $this->token['token'], 'Content-Type' => 'application/json'],
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
        /** @var ProductProvider $productProvider */
        $productProvider = $this->getProductProvider();

        static::createClient()->request(
            'DELETE',
            parent::API.'product_providers/'.$productProvider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }

}