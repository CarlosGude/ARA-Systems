<?php

namespace App\Tests\Front\Management\Admin;

use App\Entity\Provider;
use App\Tests\Front\BaseTest;

class ProviderManagementTest extends BaseTest
{
    public function testListProviders(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'provider']));

        $count = $crawler->filter('.provider-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateProvider(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'provider']));

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake2@gmail.com',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);
        $form['provider[email]']->setValue($provider['email']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el proveedor Test Provider correctamente.',
            trim($successLabel->html())
        );

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertNotNull($provider);
    }

    public function testProviderEdited(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        /** @var Provider $providerToEdit */
        $providerToEdit = $this->getRepository(Provider::class)->findOneBy(['name' => 'Another Provider']);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'provider', 'id' => $providerToEdit->getId()])
        );

        $provider = [
            'name' => 'Test provider updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el proveedor Test provider updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveProvider(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'provider']));

        $count = $crawler->filter('.delete')->count();
        self::assertEquals(0, $count);
    }
}
