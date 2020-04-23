<?php

namespace App\Tests\Front\Validation;

use App\Tests\Front\BaseTest;
use Symfony\Component\DomCrawler\Form;

class UserValidationTest extends BaseTest
{
    public function testNoRepeatedEmail(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/user');

        $user = [
            'name' => 'Test User',
            'email' => 'carlos.sgude@gmail.com',
            'password' => 'password',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor ya se ha utilizado.', $errorSpan->html());
    }

    public function testNoValidEmail(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/user');

        $user = [
            'name' => 'Test User',
            'email' => 'fake',
            'password' => 'password',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no es una dirección de email válida.', $errorSpan->html());
    }

    public function testNoEmptyEmail(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/user');

        $user = [
            'name' => 'Test User',
            'password' => 'password',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testNoEmptyPassword(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/user');

        $user = [
            'name' => 'Test User',
            'email' => 'fakeemail@email.com',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }
}
