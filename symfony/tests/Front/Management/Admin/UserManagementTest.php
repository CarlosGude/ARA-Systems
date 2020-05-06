<?php

namespace App\Tests\Front\Roles\Admin;

use App\Entity\User;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class UserManagementTest extends BaseTest
{
    public function testListUsers(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'user']));

        $count = $crawler->filter('.user-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(9, $count);
        self::assertEquals(9, $total);
    }

    public function testRemoveUsers(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['name' => 'Another User']);
        $client->request('GET', $this->generatePath('front_delete', [
            'entity' => 'user',
            'id' => $user->getId(),
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateUser(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'user']));
        self::assertCount(0, $crawler->filter('#client_company'));
        $user = [
            'name' => 'Another Test User',
            'email' => 'fake2@email.com',
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[profile]']->setValue($user['profile']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el usuario Another Test User correctamente.',
            trim($successLabel->html())
        );

        $user = $this->getRepository(User::class)->findOneBy(['email' => 'fake2@email.com']);

        self::assertNotNull($user);
    }

    public function testUserEdited(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        /** @var User $userToEdit */
        $userToEdit = $this->getRepository(User::class)->findOneBy(['email' => 'fake2@email.com']);

        $crawler = $client->request('GET', $this->generatePath('front_edit', ['entity' => 'user', 'id' => $userToEdit->getId()]));

        $user = [
            'name' => 'Test User Edited',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el usuario Test User Edited correctamente.',
            trim($successLabel->html())
        );
    }
}
