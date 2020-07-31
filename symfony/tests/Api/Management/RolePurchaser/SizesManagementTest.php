<?php

namespace App\Tests\Api\Management\RolePurchaser;

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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $response = static::createClient()->request('GET', parent::API.'sizes', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(10, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadASize(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Size $color */
        $size = $this->getSizeData('The Size 1',$this->getCompany('The Company'));

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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Size $size */
        $size = $this->getSizeData('The Size 1',$this->getCompany('The Company'));

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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Size $client */
        $size = $this->getSizeData('The Size 2',$this->getCompany('The Company'));

        static::createClient()->request('DELETE', parent::API.'sizes/'.$size->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
    }

}
