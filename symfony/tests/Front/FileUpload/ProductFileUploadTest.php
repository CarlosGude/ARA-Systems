<?php

namespace App\Tests\Front\FileUpload;

use App\Entity\Category;
use App\Entity\MediaObject;
use App\Entity\Product;
use App\Tests\Front\BaseTest;

class ProductFileUploadTest extends BaseTest
{
    public function testCreateProductWithPrincipalImage(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'product']));

        $product = [
            'name' => 'Test Product',
            'price' => '20.00',
            'barcode' => random_int(10000, 9999999),
            'location' => 'Location',
            'category' => $this->getRepository(Category::class)->findOneBy(['name' => 'The Category']),
            'image' => $this->getFile('logo.png', 'principal.png'),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['product[name]']->setValue($product['name']);
        $form['product[image]']->setValue($product['image']);
        $form['product[price]']->setValue($product['price']);
        $form['product[location]']->setValue($product['location']);
        $form['product[barcode]']->setValue($product['barcode']);
        $form['product[category]']->setValue($product['category']->getId());

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el producto Test Product correctamente.',
            trim($successLabel->html())
        );

        $product = $this->getRepository(Product::class)->findOneBy(['name' => 'Test Product']);

        self::assertInstanceOf(Product::class, $product);
        self::assertEquals(1, $client->getCrawler()->filter('img#principal')->count());
        self::assertInstanceOf(MediaObject::class, $product->getImage());
    }

    public function testRemovePrincipalImage(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Product $product */
        $product = $this->getRepository(Product::class)->findOneBy(['name' => 'Test Product']);
        $url = $this->generatePath('front_edit', ['entity' => 'product', 'id' => $product->getId()]);
        $crawler = $client->request('GET', $url);
        $removeImage = $crawler->filter('a.delete')->first();
        $client->request('POST', $removeImage->attr('data-href'));

        self::assertInstanceOf(Product::class, $product);
        self::assertEquals(0, $client->getCrawler()->filter('img#principal')->count());
    }
}
