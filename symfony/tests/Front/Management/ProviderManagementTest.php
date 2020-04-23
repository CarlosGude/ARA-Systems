<?php

namespace App\Tests\Front\Management;

use App\Entity\Provider;
use App\Tests\Front\BaseTest;

class ProviderManagementTest extends BaseTest
{
    public function testListProviders(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/provider');

        $count = $crawler->filter('.provider-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(11, $total);
    }

    public function testCreateProvider(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/provider');

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake@gmail.com'
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
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        /** @var Provider $providerToEdit */
        $providerToEdit = $this->getRepository(Provider::class)->findOneBy(['name' => 'The Provider']);

        $crawler = $client->request('GET', '/edit/provider/'.$providerToEdit->getId());

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
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/provider');

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(10, $client->getCrawler()->filter('.provider-tr')->count());
    }
}
