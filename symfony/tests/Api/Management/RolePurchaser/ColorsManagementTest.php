<?php

namespace App\Tests\Api\Management\RolePurchaser;

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
    public function testReadAllColors(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $response = static::createClient()->request('GET', parent::API.'colors', [
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
    public function testReadAColor(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Color $color */
        $color = $this->getColor('The Color 1',$this->getCompany('The Company'));

        $response = static::createClient()->request('GET', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $color = [
            'name' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'colors', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Color $color */
        $color = $this->getColor('The Color 1',$this->getCompany('The Company'));

        $response = static::createClient()->request('PUT', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Color']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals('Fake Color', $response['name']);
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAColor(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Color $client */
        $color = $this->getColor('The Color 2',$this->getCompany('The Company'));

        static::createClient()->request('DELETE', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
    }

}
