<?php


namespace App\Tests\Functional\Product;

use App\Entity\Category;
use App\Entity\Company;
use App\Tests\Functional\User\ManagementTest;

class ValidateTest extends ManagementTest
{
    public function testNameRequired()
    {
        $category = static::$container->get('doctrine')
            ->getRepository(Category::class)
            ->findOneBy(['name' => 'The Category']);

        $company = static::$container->get('doctrine')
            ->getRepository(Company::class)
            ->findOneBy(['name' => 'The Company']);

        $product = [
            'user' => parent::API . 'users/' . $this->getUser()->getId(),
            'company' => parent::API . 'companies/' . $company->getId(),
            'category' => parent::API . 'categories/' . $category->getId(),
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
        $company = static::$container->get('doctrine')
            ->getRepository(Company::class)
            ->findOneBy(['name' => 'The Company']);

        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API . 'users/' . $this->getUser()->getId(),
            'company' => parent::API . 'companies/' . $company->getId(),
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
        $category = static::$container->get('doctrine')
            ->getRepository(Category::class)
            ->findOneBy(['name' => 'The Category']);

        $product = [
            'name' => 'test',
            'description' => 'test',
            'user' => parent::API . 'users/' . $this->getUser()->getId(),
            'category' => parent::API . 'categories/' . $category->getId(),
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
}
