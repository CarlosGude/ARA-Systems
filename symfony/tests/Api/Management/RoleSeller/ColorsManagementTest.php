<?php

namespace App\Tests\Api\Management\RoleSeller;

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
        $token = $this->getToken(parent::LOGIN_SELLER);
        static::createClient()->request('GET', parent::API.'colors', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAColor(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Color $color */
        $color = $this->getColor('The Color 1',$this->getCompany('The Company'));

        static::createClient()->request('GET', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnColor(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        $color = [
            'name' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        static::createClient()->request('POST', parent::API.'colors', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($color),
        ]);
        self::assertResponseStatusCodeSame(400);
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
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Color $color */
        $color = $this->getColor('The Color 1',$this->getCompany('The Company'));

        static::createClient()->request('PUT', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Color']),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAColor(): void
    {
        $token = $this->getToken(parent::LOGIN_SELLER);
        /** @var Color $client */
        $color = $this->getColor('The Color 2',$this->getCompany('The Company'));

        static::createClient()->request('DELETE', parent::API.'colors/'.$color->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

}
