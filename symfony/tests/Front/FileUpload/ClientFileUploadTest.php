<?php

namespace App\Tests\Front\FileUpload;

use App\Entity\Client;
use App\Entity\MediaObject;
use App\Tests\Front\BaseTest;

class ClientFileUploadTest extends BaseTest
{
    public function testCreateClientWithAvatar(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'client']));

        $clientData = [
            'name' => 'Test Client',
            'image' => $this->getFile('logo.png', 'avatar.png'),
            'email' => 'fake@email.com',
            'identification' => 333225,
            'address' => 'Fake street 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['client[name]']->setValue($clientData['name']);
        $form['client[image]']->setValue($clientData['image']);
        $form['client[email]']->setValue($clientData['email']);
        $form['client[identification]']->setValue($clientData['identification']);
        $form['client[address]']->setValue($clientData['address']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el cliente Test Client correctamente.',
            trim($successLabel->html())
        );

        $clientData = $this->getRepository(Client::class)->findOneBy(['name' => 'Test Client']);

        self::assertInstanceOf(Client::class, $clientData);
        self::assertInstanceOf(MediaObject::class, $clientData->getImage());
        self::assertEquals(1, $client->getCrawler()->filter('img#avatar')->count());
    }
}
