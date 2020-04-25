<?php

namespace App\Tests\Front\Roles\Admin;

use App\Entity\Category;
use App\Entity\Company;
use App\Tests\Front\BaseTest;

class CategoryManagementTest extends BaseTest
{

    public function testListCategories(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity'=>'category']));
        $count = $crawler->filter('.category-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(9, $count);
        self::assertEquals(9, $total);
    }

    public function testCreateCategory(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'category']));
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
        $client = $this->login(parent::LOGIN_ADMIN);

        /** @var Category $categoryToEdit */
        $categoryToEdit = $this->getRepository(Category::class)->findOneBy([
            'name' => 'Category 1',
            'company'=> $this->getCompany('Another company')
            ]);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity'=>'category','id' => $categoryToEdit->getId()])
        );

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
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity'=> 'category']));

        $client->request('POST', $crawler->filter('.delete')->first()->attr('data-href'));

        self::assertEquals(9, $client->getCrawler()->filter('.category-tr')->count());
    }
}
