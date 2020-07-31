<?php

namespace App\Tests\Front\Management\RoleGod;

use App\Entity\Client;
use App\Entity\Color;
use App\Tests\Front\BaseTest;

class ColorManagementTest extends BaseTest
{
    public function testListColor(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'color']));

        $count = $crawler->filter('.color-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateColor(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'color']));

        $colorData = [
            'name' => 'Test color'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['color[name]']->setValue($colorData['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el color Test color correctamente.',
            trim($successLabel->html())
        );

        $color = $this->getRepository(Color::class)->findOneBy(['name' => 'Test color']);

        self::assertNotNull($color);
    }

    public function testColorEdited(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Color $colorToEdit */
        $colorToEdit = $this->getRepository(Color::class)->findOneBy(['name' => 'The color']);

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
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'color']));

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(10, $client->getCrawler()->filter('.color-tr')->count());
    }
}
