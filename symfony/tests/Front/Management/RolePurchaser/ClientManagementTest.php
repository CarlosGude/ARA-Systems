<?php

namespace App\Tests\Front\Management\RolePurchaser;

use App\Entity\Client;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ClientManagementTest extends BaseTest
{
    public function testListClients(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));

        $response = $client->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateClient(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);
        $client->request('GET', $this->generatePath('front_create', ['entity' => 'client']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRemoveClient(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Client $clientToRemove */
        $clientToRemove = $this->getRepository(Client::class)->findOneBy(['name' => 'Another Client']);
        $url = $this->generatePath('front_delete', ['entity' => 'client', 'id' => $clientToRemove->getId()]);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testClientEdited(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Client $clientToEdit */
        $clientToEdit = $this->getRepository(Client::class)->findOneBy(['name' => 'Another Client']);
        $url = $this->generatePath('front_edit', ['entity' => 'client', 'id' => $clientToEdit->getId()]);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
