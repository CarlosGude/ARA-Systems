<?php

namespace App\Tests\Api\Management\RoleSeller;

use App\Entity\Product;
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
class ProductsManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllProducts(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $response = static::createClient()->request('GET', parent::API.'products', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(11, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAProduct(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Product $product */
        $product = $this->getProduct();

        $response = static::createClient()->request('GET', parent::API.'products/'.$product->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $product->getName(),
            $response['name'],
            'The expected name was '.$response['name'].' but '.$product->getName().' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnProduct(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $company = $this->getCompany('Another Company');
        $product = [
            'name' => 'test',
            'description' => 'test',
            'tax' => 21,
            'price' => 21,
            'minStock' => 0,
            'maxStock' => 999,
            'user' => parent::API.'users/'.$this->getUserByEmail('user@fakemail.com')->getId(),
            'company' => parent::API.'companies/'.$company->getId(),
            'category' => parent::API.'categories/'.$this->getCategory('Category 1', $company)->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $product['name'],
            $response['name'],
            'The expected name was '.$response['name'].' but '.$response['name'].' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAProduct(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Product $product */
        $product = $this->getProduct('Product 1', $this->getCompany('Another Company'));

        $response = static::createClient()->request(
            'PUT',
            parent::API.'products/'.$product->getId(),
            ['headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode(['name' => 'Fake Product']),
            ]
        );

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Product',
            $response['name'],
            'The expected name was '.$response['name'].' but '.$product->getName().' has found'
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAProduct(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Product $product */
        $product = $this->getProduct('Product 1', $this->getCompany('Another company'));

        static::createClient()->request('DELETE', parent::API.'products/'.$product->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
