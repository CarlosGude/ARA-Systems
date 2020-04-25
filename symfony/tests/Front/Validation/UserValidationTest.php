<?php

namespace App\Tests\Front\Validation;

use App\Entity\User;
use App\Tests\Front\BaseTest;

class UserValidationTest extends BaseTest
{
    public function testNoRepeatedEmail(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'email' => 'carlos.sgude@gmail.com',
            'profile'=> User::PROFILE_ADMIN,
            'password' => 'password',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[profile]']->setValue($user['profile']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message');

        self::assertEquals(1, $errorSpan->count());
        self::assertEquals('Este valor ya se ha utilizado.', $errorSpan->first()->html());
    }

    public function testNoValidEmail(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'email' => 'fake',
            'password' => 'password',
            'profile'=> User::PROFILE_ADMIN,
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[profile]']->setValue($user['profile']);
        $form['user[email]']->setValue($user['email']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message');

        self::assertEquals(1, $errorSpan->count());
        self::assertEquals('Este valor no es una dirección de email válida.', $errorSpan->first()->html());
    }

    public function testNoEmptyEmail(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'password' => 'password',
            'profile'=> User::PROFILE_ADMIN,
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[password]']->setValue($user['password']);
        $form['user[profile]']->setValue($user['profile']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message');

        self::assertEquals(1, $errorSpan->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->first()->html());
    }

    public function testNoEmptyPassword(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'email' => 'fakeemail@email.com',
            'profile'=> User::PROFILE_ADMIN,
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[profile]']->setValue($user['profile']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message');

        self::assertEquals(1, $errorSpan->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->first()->html());
    }

    public function testNoEmptyProfile(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'user']));

        $user = [
            'name' => 'Test User',
            'email' => 'fakeemail@email.com',
            'password' => 'password'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message');

        self::assertEquals(1, $errorSpan->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->first()->html());
    }
}
