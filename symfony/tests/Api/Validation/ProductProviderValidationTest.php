<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest.
 */
class ProductProviderValidationTest extends BaseTest
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
     * @throws TransportExceptionInterface
     */
    public function testProductRequired(): void
    {
        $productProvider = [
            'price' => 0,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'product: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $productProvider = [
            'price' => 21,
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
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
    public function testProviderRequired(): void
    {
        $productProvider = [
            'price' => 21,
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'provider: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPriceIsRequired(): void
    {
        $productProvider = [
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'price: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPriceMustGreaterThanZero(): void
    {
        $productProvider = [
            'price' => -20,
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'price: Este valor debería ser mayor o igual que 0.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPriceMustLowerThanTheProductPrice(): void
    {
        $productProvider = [
            'price' => 999999999999,
            'product' => parent::API.'products/'.$this->getProduct()->getId(),
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'product_providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($productProvider),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'price: Este valor debería ser menor o igual que 100.',
            $response['hydra:description']
        );
    }

}
