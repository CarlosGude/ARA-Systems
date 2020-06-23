<?php

namespace App\Tests\Api\Management\RolePurchaser;

use App\Entity\User;
use App\Tests\Api\BaseTest;
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
        $token = $this->getToken(parent::LOGIN_PURCHASER);

        static::createClient()->request('GET', parent::API.'users', [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var User $user */
        $user = $this->getUserByEmail('user@fakemail.com');

        static::createClient()->request('GET', parent::API.'users/'.$user->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$token['token'],
            ],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnUser(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $user = [
            'email' => 'test@email.com',
            'name' => 'test',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany('Another Company')->getId(),
        ];

        static::createClient()->request('POST', parent::API.'users', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($user),
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
    public function testEditAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var User $randomUser */
        $randomUser = $this->getUserByEmail('user@fakemail.com');

        static::createClient()->request('PUT', parent::API.'users/'.$randomUser->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode(['name' => 'Fake User']),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteAUser(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $user = $this->getUserByEmail('another_user@fakemail.com');

        static::createClient()->request('DELETE', parent::API.'users/'.$user->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
