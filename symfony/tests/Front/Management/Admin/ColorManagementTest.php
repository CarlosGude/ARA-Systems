<?php

namespace App\Tests\Front\Management\Admin;

use App\Entity\Client;
use App\Entity\Color;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ColorManagementTest extends BaseTest
{
    public function testListColors(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'color']));

        $count = $crawler->filter('.color-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(9, $count);
        self::assertEquals(9, $total);
    }

    public function testCreateColor(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'color']));

        $clientData = [
            'name' => 'Test color 2'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['color[name]']->setValue($clientData['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el color Test color 2 correctamente.',
            trim($successLabel->html())
        );

        $color = $this->getRepository(Color::class)->findOneBy(['name' => 'Test color 2']);

        self::assertNotNull($color);
    }

    public function testColorEdited(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        /** @var Color $colorToEdit */
        $colorToEdit = $this->getRepository(Color::class)->findOneBy([
            'name' => 'Another color 1',
            'company' => $this->getCompany('Another Company')
        ]);

        $crawler = $client->request(
            'GET',
            $this->generatePath(
                'front_edit',
                ['entity' => 'color', 'id' => $colorToEdit->getId()]
            )
        );

        $colorUpdated = [
            'name' => 'Test Color Updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['color[name]']->setValue($colorUpdated['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el color Test Color Updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveColor(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));
        /** @var Color $colorToEdit */
        $colorToEdit = $this->getRepository(Color::class)->findOneBy([
            'name' => 'Another color 2',
            'company' => $this->getCompany('Another Company')
        ]);
        $client->request('GET', $this->generatePath('front_delete', [
            'entity' => 'color',
            'id' => $colorToEdit->getId(),
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
