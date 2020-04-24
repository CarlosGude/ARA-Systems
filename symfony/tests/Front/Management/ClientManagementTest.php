<?php

namespace App\Tests\Front\Management;

use App\Entity\Client;
use App\Tests\Front\BaseTest;

class ClientManagementTest extends BaseTest
{
    public function testListCategories(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_list',['entity'=> 'client']));

        $count = $crawler->filter('.client-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateClient(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_create',['entity'=>'client']));

        $clientData = [
            'name' => 'Test Client',
            'email' => 'fake@email.com',
            'identification' => 333225,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el cliente Test Client correctamente.',
            trim($successLabel->html())
        );

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Test Client']);

        self::assertNotNull($client);
    }

    public function testClientEdited(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        /** @var Client $clientToEdit */
        $clientToEdit = $this->getRepository(Client::class)->findOneBy(['name' => 'The Client']);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit',['entity'=>'client','id'=>$clientToEdit->getId()]
            ));

        $clientData = [
            'name' => 'Test client updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el cliente Test client updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveClient(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_list',['entity'=> 'client']));

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(10, $client->getCrawler()->filter('.client-tr')->count());
    }
}
