<?php

namespace App\Tests\Functional\Product;

use App\Entity\Product;
use App\Tests\Functional\BaseTest;
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
class ManagementTest extends BaseTest
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
    public function testReadAllProducts(): void
    {
        $response = static::createClient()->request('GET', parent::API.'products', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token'],
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
        /** @var Product $product */
        $product = $this->getProduct();

        $response = static::createClient()->request('GET', parent::API.'products/'.$product->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token'],
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
        $product = [
            'name' => 'test',
            'description' => 'test',
            'tax' => 21,
            'price' => 21,
            'minStock' => 0,
            'stockMax' => 999,
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

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
        /** @var Product $product */
        $product = $this->getProduct();

        $response = static::createClient()->request(
            'PUT',
            parent::API.'products/'.$product->getId(),
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
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
     * @throws TransportExceptionInterface
     */
    public function testDeleteAProduct(): void
    {
        /** @var Product $product */
        $product = $this->getProduct('Product 1', $this->getCompany('Another company'));

        static::createClient()->request('DELETE', parent::API.'products/'.$product->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddProvider(): void
    {
        $response = static::createClient()->request('PUT', parent::API.'products/'.$this->getProduct()->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode([
                'providers' => [parent::API.'providers/'.$this->getProvider()->getId()],
                'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            ]),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $this->getProvider()->getName(),
            $response['providers'][0]['name'],
            'The expected name was '.$response['providers'][0]['name'].' but '.$this->getProvider()->getName().' has found'
        );
    }
}
