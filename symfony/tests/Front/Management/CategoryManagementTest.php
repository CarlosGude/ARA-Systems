<?php

namespace App\Tests\Front\Management;

use App\Entity\Category;
use App\Tests\Front\BaseTest;

class CategoryManagementTest extends BaseTest
{
    public function testListCategories(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/category');

        $count = $crawler->filter('.category-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(11, $total);
    }

    public function testCreateCategory(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/category');

        $category = [
            'name' => 'Test Category',
            'tax' => Category::IVA_8,
            'minStock' => 1,
            'maxStock' => 100,
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['category[name]']->setValue($category['name']);
        $form['category[tax]']->setValue($category['tax']);
        $form['category[minStock]']->setValue($category['minStock']);
        $form['category[maxStock]']->setValue($category['maxStock']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado la categoría Test Category correctamente.',
            trim($successLabel->html())
        );

        $category = $this->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);

        self::assertNotNull($category);
    }

    public function testCategoryEdited(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        /** @var Category $categoryToEdit */
        $categoryToEdit = $this->getRepository(Category::class)->findOneBy(['name' => 'The Category']);

        $crawler = $client->request('GET', '/edit/category/'.$categoryToEdit->getId());

        $category = [
            'name' => 'Test category updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['category[name]']->setValue($category['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado la categoría Test category updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveCategory(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/category');

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(10, $client->getCrawler()->filter('.category-tr')->count());
    }
}
