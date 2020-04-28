<?php


namespace App\Tests\Front\FileUpload;


use App\Entity\Category;
use App\Entity\Company;
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
            'tax' => Product::IVA_8,
            'price' => '20.00',
            'category' => $this->getRepository(Category::class)->findOneBy(['name' => 'The Category']),
            'minStock' => 1,
            'maxStock' => 100,
            'image' => $this->getFile('logo.png','principal.png')
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['product[name]']->setValue($product['name']);
        $form['product[image]']->setValue($product['image']);
        $form['product[tax]']->setValue($product['tax']);
        $form['product[price]']->setValue($product['price']);
        $form['product[category]']->setValue($product['category']->getId());
        $form['product[minStock]']->setValue($product['minStock']);
        $form['product[maxStock]']->setValue($product['maxStock']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el producto Test Product correctamente.',
            trim($successLabel->html())
        );

        $product = $this->getRepository(Product::class)->findOneBy(['name' => 'Test Product']);

        self::assertInstanceOf(Product::class,$product);
        self::assertEquals(1,$client->getCrawler()->filter('img#principal')->count());
        self::assertInstanceOf(MediaObject::class, $product->getImage());
    }
}