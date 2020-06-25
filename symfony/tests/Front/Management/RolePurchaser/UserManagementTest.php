<?php

namespace App\Tests\Front\Management\RolePurchaser;

use App\Entity\User;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class UserManagementTest extends BaseTest
{
    public function testRemoveUsers(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['name' => 'Carlos Gude']);
        $url = $this->generatePath('front_delete', ['entity' => 'user', 'id' => $user->getId()]);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testListUsers(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $url = $this->generatePath('front_list', ['entity' => 'user']);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateUser(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $client->request('GET', $this->generatePath('front_create', ['entity' => 'user']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testUserEdited(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => 'carlos.sgude@gmail.com']);

        $client->request('GET', $this->generatePath('front_edit', ['entity' => 'user', 'id' => $user->getId()]));
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
