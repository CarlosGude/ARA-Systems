<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidationTest.
 */
class ProductValidationTest extends BaseTest
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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
     * @throws \Exception
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
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals('price: Este valor debería ser mayor que 0.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testValidationInherit(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
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

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductLengthShouldBeAFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productLength' => 'invalid',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productLength" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductHeightShouldBeAFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productHeight' => 'invalid',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productHeight" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductWidthShouldBeAFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productWidth' => 'invalid',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productWidth" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductWidthShouldBeAPositiveFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productWidth' => '-2',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productWidth" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductLengthShouldBeAPositiveFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productLength' => '-1',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productLength" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductHeightShouldBeAPositiveFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'productHeight' => '-3',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "productHeight" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductKilogramsShouldBeAPositiveFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'kilograms' => '-3',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "kilograms" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductKilogramsShouldBeAFloat(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'kilograms' => 'integer',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "kilograms" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductReferenceIsRequired(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'kilograms' => '20',
            'location' => 'Location',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "kilograms" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testProductLocationIsRequired(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'kilograms' => '20',
            'barcode' => random_int(10000, 9999999),
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "kilograms" attribute must be "float", "string" given.', $response['hydra:description']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function testReferenceShouldBeAInteger(): void
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'price' => 20,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'category' => parent::API.'categories/'.$category->getId(),
            'user' => parent::API.'users/'.$this->getGodUser()->getId(),
            'kilograms' => '20',
            'reference' => 'reference',
        ];

        $response = static::createClient()->request('POST', parent::API.'products', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($product),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');

        $this->assertEquals('The type of the "kilograms" attribute must be "float", "string" given.', $response['hydra:description']);
    }
}
