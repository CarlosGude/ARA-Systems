<?php

namespace App\Tests\Front\Management\RolePurchaser;

use App\Entity\Client;
use App\Entity\Size;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class SizeManagementTest extends BaseTest
{
    public function testListSize(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'size']));

        $count = $crawler->filter('.size-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateSize(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'size']));

        $sizeData = [
            'name' => 'Test size',
            'type' => Size::SIZE_TYPE_CLOTHING_SIZE
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['size[name]']->setValue($sizeData['name']);
        $form['size[type]']->setValue($sizeData['type']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el tamaño/talla Test size correctamente.',
            trim($successLabel->html())
        );

        $size = $this->getRepository(Size::class)->findOneBy(['name' => 'Test size']);

        self::assertNotNull($size);
    }

    public function testSizeEdited(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Size $sizeToEdit */
        $sizeToEdit = $this->getRepository(Size::class)->findOneBy(['name' => 'No size']);

        $crawler = $client->request(
            'GET',
            $this->generatePath(
                'front_edit',
                ['entity' => 'size', 'id' => $sizeToEdit->getId()]
            )
        );

        $sizeUpdated = [
            'name' => 'Test Size Updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['size[name]']->setValue($sizeUpdated['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el tamaño/talla Test Size Updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveSize(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Size $size */
        $size = $this->getRepository(Size::class)->findOneBy(['name' => 'The Size 3']);
        $client->request('GET', $this->generatePath('front_list', ['entity' => 'size']));
        $client->request(
            'GET',
            $this->generatePath('front_delete', ['entity' => 'size', 'id' => $size->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
