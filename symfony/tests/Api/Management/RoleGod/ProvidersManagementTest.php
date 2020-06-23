<?php

namespace App\Tests\Api\Management\RoleGod;

use App\Entity\Provider;
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
class ProvidersManagementTest extends BaseTest
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
    public function testReadAllProviders(): void
    {
        $response = static::createClient()->request('GET', parent::API.'providers', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(20, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAProvider(): void
    {
        /** @var Provider $provider */
        $provider = $this->getProvider();

        $response = static::createClient()->request('GET', parent::API.'providers/'.$provider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $provider->getName(),
            $response['name'],
            'The expected name was '.$response['name'].' but '.$provider->getName().' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnProvider(): void
    {
        $provider = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@gmail.com',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'providers', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($provider),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $provider['name'],
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
    public function testEditAProvider(): void
    {
        /** @var Provider $provider */
        $provider = $this->getProvider();

        $response = static::createClient()->request('PUT', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode(['name' => 'Fake Provider']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Provider',
            $response['name'],
            'The expected name was '.$response['name'].' but '.$provider->getName().' has found'
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteProvider(): void
    {
        /** @var Provider $provider */
        $provider = $this->getProvider('Provider 1');

        static::createClient()->request('DELETE', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
