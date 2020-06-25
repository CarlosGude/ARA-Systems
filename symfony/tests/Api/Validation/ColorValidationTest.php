<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ColorValidationTest extends BaseTest
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
     * @throws TransportExceptionInterface
     */
    public function testNameRequired(): void
    {
        $client = [
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'colors',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);


        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $client = ['name' => 'test',];

        $response = static::createClient()->request(
            'POST',
            parent::API.'colors',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }
}
