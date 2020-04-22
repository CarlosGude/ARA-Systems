<?php

namespace App\Tests\Api\Product;

use App\Tests\Api\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest.
 */
class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testNameRequired(): void
    {
        $product = [
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
            'price' => 20,
            'minStock' => 0,
            'maxStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCategoryRequired(): void
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'price' => 20,
            'minStock' => 0,
            'maxStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'category: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
            'price' => 22,
            'minStock' => 0,
            'maxStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testStockMaxLowerThanMinStock(): void
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
            'maxStock' => 0,
            'minStock' => 100,
            'price' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'maxStock: Este valor debería ser mayor o igual que 100.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPriceIsRequired(): void
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
            'minStock' => 0,
            'maxStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'price: Este valor debería ser mayor que 0.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPriceIsGreaterThanZero(): void
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$this->getCategory()->getId(),
            'minStock' => 0,
            'maxStock' => 100,
            'price' => -100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'price: Este valor debería ser mayor que 0.',
            $response['hydra:description']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testValidateInherit(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'minStock' => 0,
            'maxStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $category->getMinStock(),
            $response['minStock'],
            'The expected name was '.$category->getMinStock().' but '.$response['minStock'].' has found'
        );

        $this->assertEquals(
            $category->getMaxStock(),
            $response['maxStock'],
            'The expected name was '.$category->getMaxStock().' but '.$response['maxStock'].' has found'
        );

        $this->assertEquals(
            $category->getTax(),
            $response['tax'],
            'The expected name was '.$category->getTax().' but '.$response['tax'].' has found'
        );
    }
}
