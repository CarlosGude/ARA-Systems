<?php

namespace App\Tests\Api\User;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LoginTest extends BaseTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testAuthorisationIsRequired(): void
    {
        static::createClient()->request('GET', self::API);

        self::assertResponseStatusCodeSame(401, 'Authorisation is not required to access to API');
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testLogin(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'carlos.sgude@gmail.com',
            'password' => 'pasalacabra',
        ]]);

        $response = json_decode($response->getContent(), true);

        $this->assertIsArray($response, 'The response is not an array.');
        $this->assertArrayHasKey('token', $response, 'The response has not token.');
        self::assertResponseIsSuccessful();
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testFailLogin(): void
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'fake@email.com',
            'password' => '1234',
        ]]);
        self::assertResponseStatusCodeSame(401, 'The response in not equals at 401');

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertIsArray($response, 'The response is not an array.');
        $this->assertArrayHasKey('message', $response, 'The response has not have a message.');
        $this->assertEquals(
            'Invalid credentials.',
            $response['message'],
            'The response is '.$response['message'].' "Invalid credentials." was expected.'
        );
    }
}
