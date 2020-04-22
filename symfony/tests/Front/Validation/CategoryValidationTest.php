<?php

namespace App\Tests\Front\Validation;

use App\Entity\Category;
use App\Tests\Front\BaseTest;
use Symfony\Component\DomCrawler\Form;

class CategoryValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/category');

        $category = [
            'tax' => Category::IVA_8,
            'minStock' => 1,
            'maxStock' => 100,
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['category[tax]']->setValue($category['tax']);
        $form['category[minStock]']->setValue($category['minStock']);
        $form['category[maxStock]']->setValue($category['maxStock']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals($errorSpan->html(), 'Este valor no debería estar vacío.');
    }

    public function testMinStockGreaterThanOne(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/category');

        $category = [
            'name' => 'The Name',
            'tax' => Category::IVA_8,
            'maxStock' => 1,
            'minStock' => '-1',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['category[name]']->setValue($category['name']);
        $form['category[tax]']->setValue($category['tax']);
        $form['category[maxStock]']->setValue($category['maxStock']);
        $form['category[minStock]']->setValue($category['minStock']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals($errorSpan->html(), 'Este valor debería ser mayor o igual que 0.');
    }

    public function testMaxStockGreaterThanOne(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/category');

        $category = [
            'name' => 'The Name',
            'tax' => Category::IVA_8,
            'maxStock' => '-1',
            'minStock' => 1,
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['category[name]']->setValue($category['name']);
        $form['category[tax]']->setValue($category['tax']);
        $form['category[maxStock]']->setValue($category['maxStock']);
        $form['category[minStock]']->setValue($category['minStock']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals($errorSpan->html(), 'Este valor debería ser mayor o igual que 1.');
    }

    public function testMaxStockLowerThanMinStockRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/category');

        $category = [
            'name' => 'The Name',
            'tax' => Category::IVA_8,
            'maxStock' => 1,
            'minStock' => 100,
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['category[name]']->setValue($category['name']);
        $form['category[tax]']->setValue($category['tax']);
        $form['category[maxStock]']->setValue($category['maxStock']);
        $form['category[minStock]']->setValue($category['minStock']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals($errorSpan->html(), 'Este valor debería ser mayor o igual que 100.');
    }
}
