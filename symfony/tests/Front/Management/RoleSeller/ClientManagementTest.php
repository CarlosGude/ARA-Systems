<?php

namespace App\Tests\Front\Roles\RoleSeller;

use App\Entity\Client;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ClientManagementTest extends BaseTest
{
    public function testListClients(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));

        $count = $crawler->filter('.client-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateClient(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'client']));

        $clientData = [
            'name' => 'Test Client',
            'email' => 'fakeAdmin@email.com',
            'identification' => 878789,
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
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Client $clientToEdit */
        $clientToEdit = $this->getRepository(Client::class)->findOneBy(['name' => 'Another Client']);

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
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));
        /** @var Client $clientData */
        $clientData = $this->getRepository(Client::class)->findOneBy(['name' => 'Another Client']);
        $client->request('GET', $this->generatePath('front_delete', [
            'entity' => 'client',
            'id' => $clientData->getId()
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
