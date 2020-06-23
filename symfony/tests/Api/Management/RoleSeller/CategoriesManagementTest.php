<?php

namespace App\Tests\Api\Management\RoleSeller;

use App\Entity\Category;
use App\Tests\Api\BaseTest;
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
class CategoriesManagementTest extends BaseTest
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
    public function testReadAllCategories(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);

        $response = static::createClient()->request('GET', parent::API.'categories', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
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
    public function testReadACategory(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $category = $this->getCategory();

        $response = static::createClient()->request('GET', parent::API.'categories/'.$category->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $category->getName(),
            $response['name'],
            'The expected name was '.$response['name'].' but '.$category->getName().' has found'
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
        $token = $this->getToken(parent::LOGIN_SELLER);

        $category = [
            'name' => 'test',
            'description' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'tax' => 21,
        ];

        $response = static::createClient()->request('POST', parent::API.'categories', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($category),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $category['name'],
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
    public function testEditACategory(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Category $category */
        $category = $this->getCategory('Another Category 2', $this->getCompany('Another Company'));

        $response = static::createClient()->request('PUT', parent::API.'categories/'.$category->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Category']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Category',
            $response['name'],
            'The expected name was '.$response['name'].' but '.$category->getName().' has found'
        );

        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteACategory(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Category $category */
        $category = $this->getCategory('The Category');

        static::createClient()->request('DELETE', parent::API.'categories/'.$category->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
