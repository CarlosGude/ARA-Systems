<?php


namespace App\Tests\Functional\Provider;

use App\Entity\Provider;
use App\Tests\Functional\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest
 * @package App\Tests\Functional\Provider
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
        $response = static::createClient()->request('GET', parent::API . 'providers', [
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
    public function testReadARandomProvider(): void
    {
        $providers = static::$container->get('doctrine')->getRepository(Provider::class)->findAll();

        /** @var Provider $randomProvider */
        $randomProvider = $providers[array_rand($providers)];

        $response = static::createClient()->request('GET', parent::API . 'providers/' . $randomProvider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token']
            ]
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $randomProvider->getName(),
            $response['name'],
            'The expected name was ' . $response['name'] . ' but ' . $randomProvider->getName() . ' has found'
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
            'company' => parent::API . 'companies/' . $this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API . 'providers', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($provider)
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $provider['name'],
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
    public function testEditARandomProvider(): void
    {
        $providers = static::$container->get('doctrine')->getRepository(Provider::class)->findAll();

        /** @var Provider $randomProvider */
        $randomProvider = $providers[array_rand($providers)];

        $response = static::createClient()->request('PUT', parent::API . 'providers/' . $randomProvider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode(['name' => 'Fake Provider'])
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Provider',
            $response['name'],
            'The expected name was ' . $response['name'] . ' but ' . $randomProvider->getName() . ' has found'
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteProvider(): void
    {
        /** @var Provider $provider */
        $provider = $this->getProvider('The Provider');

        static::createClient()->request('DELETE', parent::API . 'providers/' . $provider->getId(), [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
            ]
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
