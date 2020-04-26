<?php

namespace App\Tests\Api\Management\RoleGod;

use App\Entity\Client;
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
class ClientsManagementTest extends BaseTest
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
    public function testReadAllClients(): void
    {
        $response = static::createClient()->request('GET', parent::API.'clients', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
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
    public function testReadAClient(): void
    {
        /** @var Client $client */
        $client = $this->getPurchaseClient();

        $response = static::createClient()->request('GET', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $client->getName(),
            $response['name'],
            'The expected name was '.$response['name'].' but '.$client->getName().' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnClient(): void
    {
        $client = [
            'name' => 'test',
            'email' => 'fake@email.com',
            'identification' => '3616884',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'clients', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($client),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $client['name'],
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
    public function testEditAClient(): void
    {
        /** @var Client $client */
        $client = $this->getPurchaseClient();

        $response = static::createClient()->request('PUT', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Client']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Client',
            $response['name'],
            'The expected name was '.$response['name'].' but '.$client->getName().' has found'
        );

        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteAClient(): void
    {
        /** @var Client $client */
        $client = $this->getPurchaseClient('The Client To Delete');

        static::createClient()->request('DELETE', parent::API.'clients/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
