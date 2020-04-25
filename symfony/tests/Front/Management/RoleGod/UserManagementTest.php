<?php

namespace App\Tests\Front\Roles\RoleGod;

use App\Entity\User;
use App\Tests\Front\BaseTest;

class UserManagementTest extends BaseTest
{
    public function testRemoveUsers(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity'=>'user']));

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(11, $client->getCrawler()->filter('.table')->first()->attr('data-total'));
    }

    public function testListUsers(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity'=>'user']));

        $count = $crawler->filter('.user-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(11, $total);
    }

    public function testCreateUser(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'email' => 'fake@email.com',
            'profile'=> User::PROFILE_ADMIN,
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
            'Se ha creado el usuario Test User correctamente.',
            trim($successLabel->html())
        );

        $user = $this->getRepository(User::class)->findOneBy(['email' => 'fake@email.com']);

        self::assertNotNull($user);
    }

    public function testUserEdited(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var User $userToEdit */
        $userToEdit = $this->getRepository(User::class)->findOneBy(['email' => 'fake@email.com']);

        $crawler = $client->request('GET', $this->generatePath('front_edit', ['entity'=>'user','id'=>$userToEdit->getId()]));

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
