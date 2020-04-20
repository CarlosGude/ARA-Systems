<?php

namespace App\Tests\Api\User;

use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest.
 */
class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testRepeatedEmail(): void
    {
        $user = [
            'email' => 'carlos.sgude@gmail.com',
            'name' => 'test',
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor ya se ha utilizado.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testValidEmail(): void
    {
        $user = [
            'email' => 'Fake email',
            'name' => 'test',
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

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
    public function testEmailIsRequired(): void
    {
        $user = [
            'name' => 'test',
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'email: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testNameIsRequired(): void
    {
        $user = [
            'email' => 'test@email.com',
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPasswordIsRequired(): void
    {
        $user = [
            'email' => 'test@email.com',
            'name' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'password: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }
}
