<?php

namespace App\Tests\Front\Roles\Admin;

use App\Entity\Client;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ClientManagementTest extends BaseTest
{
    public function testListClients(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));

        $count = $crawler->filter('.client-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateClient(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'client']));
        self::assertCount(0, $crawler->filter('#client_company'));
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

        $client = $this->getRepository(Client::class)->findOneBy(['name' => 'Test Client']);

        self::assertNotNull($client);
        self::assertInstanceOf(Client::class, $client);
    }

    public function testClientEdited(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Client $clientToEdit */
        $clientToEdit = $this->getRepository(Client::class)->findOneBy(['name' => 'The Client']);

        $crawler = $client->request(
            'GET',
            $this->generatePath(
                'front_edit',
                ['entity' => 'client', 'id' => $clientToEdit->getId()]
            )
        );

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
        $client = $this->login(parent::LOGIN_ADMIN);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));
        /** @var Client $clientData */
        $clientData = $this->getRepository(Client::class)->findOneBy(['name' => 'Another Client']);
        $client->request('GET', $this->generatePath('front_delete', [
            'entity' => 'client',
            'id' => $clientData->getId(),
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
