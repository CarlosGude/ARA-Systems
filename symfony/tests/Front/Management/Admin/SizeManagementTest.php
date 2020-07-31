<?php

namespace App\Tests\Front\Management\Admin;


use App\Entity\Size;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class SizeManagementTest extends BaseTest
{
    public function testListSizes(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'size']));

        $count = $crawler->filter('.size-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(9, $count);
        self::assertEquals(9, $total);
    }

    public function testCreateSize(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

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
        $client = $this->login(parent::LOGIN_ADMIN);

        /** @var Size $sizeToEdit */
        $sizeToEdit = $this->getRepository(Size::class)->findOneBy([
            'name' => 'Another size 1',
            'company' => $this->getCompany('Another Company')
        ]);

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
        $client = $this->login(parent::LOGIN_ADMIN);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'client']));
        /** @var Size $sizeToEdit */
        $sizeToEdit = $this->getRepository(Size::class)->findOneBy([
            'name' => 'Another size 2',
            'company' => $this->getCompany('Another Company')
        ]);
        $client->request('GET', $this->generatePath('front_delete', [
            'entity' => 'size',
            'id' => $sizeToEdit->getId(),
        ]));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
