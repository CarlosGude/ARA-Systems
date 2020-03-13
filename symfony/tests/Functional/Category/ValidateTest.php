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
}
