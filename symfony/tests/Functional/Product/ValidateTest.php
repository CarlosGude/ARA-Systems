<?php


namespace App\Tests\Functional\Product;

use App\Tests\Functional\User\ManagementTest;

class ValidateTest extends ManagementTest
{
    public function testNameRequired()
    {

        $product = [
            'user' => parent::API . 'users/' . $this->getGodUser()->getId(),
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'category' => parent::API . 'categories/' . $this->getCategory()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API . 'products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($product)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testCategoryRequired()
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API . 'users/' . $this->getGodUser()->getId(),
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API . 'products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($product)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'category: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testCompanyRequired()
    {

        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API . 'users/' . $this->getGodUser()->getId(),
            'category' => parent::API . 'categories/' . $this->getCategory()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API . 'products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($product)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testStockMaxLowerThanMinStock()
    {
        $product = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'category' => parent::API . 'categories/' . $this->getCategory()->getId(),
            'maxStock' => 0,
            'minStock' => 100,
        ];

        $response = static::createClient()->request('POST', parent::API . 'products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($product)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertEquals(
            'maxStock: This value should be greater than or equal to 100.',
            $response['hydra:description']
        );
    }

    public function testValidateInherit()
    {
        $category = $this->getCategory();

        $product = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'category' => parent::API . 'categories/' . $category->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API . 'products', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($product)
        ]);

        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $category->getMinStock(),
            $response['minStock'],
            'The expected name was ' . $category->getMinStock() . ' but ' . $response['minStock'] . ' has found'
        );

        $this->assertEquals(
            $category->getMaxStock(),
            $response['maxStock'],
            'The expected name was ' . $category->getMaxStock() . ' but ' . $response['maxStock'] . ' has found'
        );

        $this->assertEquals(
            $category->getIva(),
            $response['iva'],
            'The expected name was ' . $category->getIva() . ' but ' . $response['iva'] . ' has found'
        );
    }
}
