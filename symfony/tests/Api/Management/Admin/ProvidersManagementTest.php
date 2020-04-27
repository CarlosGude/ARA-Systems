<?php

namespace App\Tests\Api\Management\Admin;

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
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllProviders(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $response = static::createClient()->request('GET', parent::API.'providers', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
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
    public function testReadAProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Provider $provider */
        $provider = $this->getProvider();

        $response = static::createClient()->request('GET', parent::API.'providers/'.$provider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
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
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $provider = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@gmail.com',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'providers', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

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
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Provider $provider */
        $provider = $this->getProvider('Another Provider', $this->getCompany('Another Provider'));

        $response = static::createClient()->request('PUT', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

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
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteProvider(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var Provider $provider */
        $provider = $this->getProvider('Another Provider', $this->getCompany('Another Provider'));

        static::createClient()->request('DELETE', parent::API.'providers/'.$provider->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
