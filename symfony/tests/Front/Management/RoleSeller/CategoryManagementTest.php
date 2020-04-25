<?php

namespace App\Tests\Front\Roles\RoleSeller;

use App\Entity\Category;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class CategoryManagementTest extends BaseTest
{
    public function testListCategories(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity'=>'category']));
        $count = $crawler->filter('.category-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(11, $total);
    }

    public function testRemoveCategory(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Category $category */
        $category = $this->getRepository(Category::class)->findOneBy([
            'name' => 'Another Category',
            'company'=>$this->getCompany('The Company')
        ]);
        $url = $this->generatePath('front_delete',['entity'=>'category','id'=> $category->getId()]);
        $client->request('GET',$url);

        $response= $client->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN,$response->getStatusCode());
    }

    public function testCreateCategory(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

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
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Category $categoryToEdit */
        $categoryToEdit = $this->getRepository(Category::class)->findOneBy(['name' => 'The Category']);

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
}
