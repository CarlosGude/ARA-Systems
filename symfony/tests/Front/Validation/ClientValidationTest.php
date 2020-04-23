<?php

namespace App\Tests\Front\Validation;

use App\Tests\Front\BaseTest;

class ClientValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'email' => 'fake@email.com',
            'identification' => 333225,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testEmailRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'name' => 'Test Client',
            'identification' => 333225,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testIdentificationRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'name' => 'Test Client',
            'email' => 'fake@email.com',
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testIdentificationUnique(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'name' => 'Test Client',
            'email' => 'fake@email.com',
            'identification' => 666,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor ya se ha utilizado.', $errorSpan->html());
    }

    public function testEmailValid(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'name' => 'Test Client',
            'email' => 'fake',
            'identification' => 789888,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no es una dirección de email válida.', $errorSpan->html());
    }

    public function testEmailUnique(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/client');

        $clientData = [
            'name' => 'Test Client',
            'email' => 'client@email.com',
            'identification' => 789888,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor ya se ha utilizado.', $errorSpan->html());
    }
}
