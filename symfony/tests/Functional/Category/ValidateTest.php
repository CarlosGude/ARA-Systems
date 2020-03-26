<?php


namespace App\Tests\Functional\Category;

use App\Tests\Functional\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest
 * @package App\Tests\Functional\Category
 */
class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testNameRequired(): void
    {
        $category = [
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId()
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: This value should not be blank.',
            $response['hydra:description']
        );
    }


    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'company: This value should not be blank.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCategoriesWithProductsCanNotBeDeleted(): void
    {
        $response = static::createClient()->request(
            'DELETE',
            parent::API . 'categories/' . $this->getCategory()->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token['token'],
                ]
            ]
        );

        $code = $response->getStatusCode();
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(
            400,
            'The response is ' . $code . ' but 400 is expected.'
        );

        $this->assertEquals(
            'The category The Category can not be deleted because it has products nested.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testIvaInt(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'tax' => 'IVA not valid'
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'The type of the "tax" attribute must be "int", "string" given.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testMinStockInt(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'minStock' => 'Stock not valid'
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'The type of the "minStock" attribute must be "int", "string" given.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testStockMaxLowerThanMinStock(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'stockMax' => 0,
            'minStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'maxStock: This value should be greater than or equal to 100.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testIvaValid(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'tax' => 999999
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'tax: The value you selected is not a valid choice.',
            $response['hydra:description']
        );
    }
}
