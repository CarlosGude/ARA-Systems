<?php

namespace App\Tests\Api\Management\Admin;

use App\Entity\Size;
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
class SizesManagementTest extends BaseTest
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
    public function testReadAllSizes(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $response = static::createClient()->request('GET', parent::API.'sizes', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(9, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadASize(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Size $size */
        $size = $this->getSizeData('Another Size 1',$this->getCompany('Another Company'));

        $response = static::createClient()->request('GET', parent::API.'sizes/'.$size->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        $response = json_decode($response->getContent(), true);
        
        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals($size->getName(), $response['name']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnSize(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $size = [
            'name' => 'test',
            'type' => Size::SIZE_TYPE_LONG,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'sizes', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($size),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals($size['name'], $response['name']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditASize(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Size $size */
        $size = $this->getSizeData('Another Size 1',$this->getCompany('Another Company'));

        $response = static::createClient()->request('PUT', parent::API.'sizes/'.$size->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Size']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals('Fake Size', $response['name']);
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteASize(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Size $client */
        $color = $this->getSizeData('Another Size 2',$this->getCompany('Another Company'));

        static::createClient()->request('DELETE', parent::API.'sizes/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
    }

}
