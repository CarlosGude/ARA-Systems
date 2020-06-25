<?php

namespace App\Tests\Api\Management\RoleGod;

use App\Entity\Color;
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
class ColorsManagementTest extends BaseTest
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
    public function testReadAllColors(): void
    {
        $response = static::createClient()->request('GET', parent::API.'colors', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(19, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAColor(): void
    {
        /** @var Color $color */
        $color = $this->getColor();

        $response = static::createClient()->request('GET', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals($color->getName(), $response['name']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnColor(): void
    {
        $color = [
            'name' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'colors', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($color),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals($color['name'], $response['name']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAColor(): void
    {
        /** @var Color $color */
        $color = $this->getColor();

        $response = static::createClient()->request('PUT', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Color']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals('Fake Color', $response['name']);
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteAColor(): void
    {
        /** @var Color $client */
        $client = $this->getColor('The Color To Delete');

        static::createClient()->request('DELETE', parent::API.'colors/'.$client->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }

}
