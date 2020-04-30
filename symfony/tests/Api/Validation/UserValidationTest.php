<?php

namespace App\Tests\Api\Validation;

use App\Entity\User;
use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class UserValidationTest extends BaseTest
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
        $user = [
            'email' => 'fake2@email.com',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
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
    public function testEmailRequired(): void
    {
        $user = [
            'name' => 'name',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProfileRequired(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'fake2@email.com',
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'profile: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPasswordRequired(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'fake2@email.com',
            'profile' => User::PROFILE_ADMIN,
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'password: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'fake2@email.com',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProfileValidRequired(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'fake2@email.com',
            'profile' => 'profile',
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];


        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'profile: El valor seleccionado no es una opción válida.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testEmailValid(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'fake',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];


        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor no es una dirección de email válida.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testEmailUnique(): void
    {
        $user = [
            'name' => 'name',
            'email' => 'carlos.sgude@gmail.com',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];


        $response = static::createClient()->request(
            'POST',
            parent::API.'users',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($user),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor ya se ha utilizado.',
            $response['hydra:description']
        );
    }
}
