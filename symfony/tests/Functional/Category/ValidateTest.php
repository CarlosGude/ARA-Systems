<?php


namespace App\Tests\Functional\Category;

use App\Tests\Functional\User\ManagementTest;

class ValidateTest extends ManagementTest
{
    public function testNameRequired()
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

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testCompanyRequired()
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

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testCategoriesWithProductsCanNotBeDeleted()
    {
        $response = static::createClient()->request(
            'DELETE',
            parent::API . 'categories/' . $this->getCategory()->getId(),
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->token['token'],
                ]
            ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'The category The Category can not be deleted because it has products nested.',
            $response['hydra:description']
        );
    }

    public function testIvaInt()
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'iva' => 'IVA not valid'
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
            'The type of the "iva" attribute must be "int", "string" given.',
            $response['hydra:description']
        );
    }

    public function testStockMinInt()
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'stockMin' => 'Stock not valid'
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
            'The type of the "stockMin" attribute must be "int", "string" given.',
            $response['hydra:description']
        );
    }

    public function testStockMaxLowerThanStockMin()
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'stockMax' => 0,
            'stockMin' => 100,
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
            'stockMax: This value should be greater than or equal to 100.',
            $response['hydra:description']
        );
    }

    public function testIvaValid()
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'iva' => 999999
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
            'iva: The value you selected is not a valid choice.',
            $response['hydra:description']
        );
    }
}
