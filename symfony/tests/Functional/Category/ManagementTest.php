<?php


namespace App\Tests\Functional\Category;

use App\Entity\Category;
use App\Tests\Functional\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest
 * @package App\Tests\Functional\Category
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
    public function testReadAllCategories(): void
    {
        $response = static::createClient()->request('GET', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token']
            ]
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
    public function testReadARandomCategory(): void
    {
        $categories = static::$container->get('doctrine')->getRepository(Category::class)->findAll();

        /** @var Category $randomCategory */
        $randomCategory = $categories[array_rand($categories)];

        $response = static::createClient()->request('GET', parent::API . 'categories/' . $randomCategory->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token']
            ]
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $randomCategory->getName(),
            $response['name'],
            'The expected name was ' . $response['name'] . ' but ' . $randomCategory->getName() . ' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnCategory(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
            'iva' => 21
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $category['name'],
            $response['name'],
            'The expected name was ' . $response['name'] . ' but ' . $response['name'] . ' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testEditARandomCategory(): void
    {
        $categories = static::$container->get('doctrine')->getRepository(Category::class)->findAll();

        /** @var Category $randomCategory */
        $randomCategory = $categories[array_rand($categories)];

        $response = static::createClient()->request('PUT', parent::API . 'categories/' . $randomCategory->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode(['name' => 'Fake Category'])
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Category',
            $response['name'],
            'The expected name was ' . $response['name'] . ' but ' . $randomCategory->getName() . ' has found'
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteARandomCategory(): void
    {

        /** @var Category $category */
        $category = $this->getCategory('Another Category');

        static::createClient()->request('DELETE', parent::API . 'categories/' . $category->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
            ]
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
