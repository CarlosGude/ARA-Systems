<?php

namespace App\Tests\Api\Management\Admin;

use App\Entity\User;
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
class UsersManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllUsers(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);

        $response = static::createClient()->request('GET', parent::API.'users', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(9, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var User $user */
        $user = $this->getUserByEmail('user@fakemail.com');

        $response = static::createClient()->request('GET', parent::API.'users/'.$user->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $user->getEmail(),
            $response['email'],
            'The expected email was '.$response['email'].' but '.$user->getEmail().' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnUser(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $user = [
            'email' => 'test@email.com',
            'name' => 'test',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $user['email'],
            $response['email'],
            'The expected email was '.$response['email'].' but '.$user['email'].' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        /** @var User $randomUser */
        $randomUser = $this->getUserByEmail('user@fakemail.com');

        $response = static::createClient()->request('PUT', parent::API.'users/'.$randomUser->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode(['name' => 'Fake User']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake User',
            $response['name'],
            'The expected email was '.$response['email'].' but '.$randomUser->getEmail().' has found'
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_ADMIN);
        $user = $this->getUserByEmail('another_user@fakemail.com');

        static::createClient()->request('DELETE', parent::API.'users/'.$user->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
