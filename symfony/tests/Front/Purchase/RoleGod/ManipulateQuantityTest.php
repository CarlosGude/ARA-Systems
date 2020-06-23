<?php

namespace App\Tests\Front\Purchase\RoleGod;

use App\Entity\Product;
use App\Tests\Front\BaseTest;

class ManipulateQuantityTest extends BaseTest
{
    public function testAddProductToPurchase(): void
    {
        $client = $this->login(parent::LOGIN_GOD);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        $line = [
            'product' => $this->getRepository(Product::class)->findOneBy(['name' => 'The Product']),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['purchase_line[product]']->setValue($line['product']->getId());

        $client->submit($form);

        $purchaseLine = $client->getCrawler()->filter('.purchase-line-tr');

        self::assertEquals(1, $purchaseLine->count());
        $line = $client->getCrawler()->filter('.purchase-line-tr');

        $total = $client->getCrawler()->filter('#total')->first()->attr('data-total');
        $taxes = $client->getCrawler()->filter('#taxes')->first()->attr('data-taxes');
        $final = $client->getCrawler()->filter('#final')->first()->attr('data-final');

        $productQuantity = $line->attr('data-quantity');
        $productPrice = $line->attr('data-price');
        $productTax = $line->attr('data-tax');

        self::assertEquals($total, $totalProduct = $productPrice * $productQuantity);
        self::assertEquals($taxes, $totalProduct * ($productTax / 100));
        self::assertEquals($final, $totalProduct * ($productTax / 100) + $totalProduct);
        self::assertEquals(1, $line->count());
        self::assertEquals(1, $productQuantity);
    }

    public function testAddQuantityToLine(): void
    {
        $client = $this->login(parent::LOGIN_GOD);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        $line = [
            'product' => $this->getRepository(Product::class)->findOneBy(['name' => 'The Product']),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['purchase_line[product]']->setValue($line['product']->getId());

        $client->submit($form);

        self::assertEquals(1, $client->getCrawler()->filter('.purchase-line-tr')->count());

        $url = $client->getCrawler()->filter('.change-quantity')->first()->attr('data-href');
        $url = str_replace('value', 10, $url);

        $client->request('GET', $url);

        $line = $client->getCrawler()->filter('.purchase-line-tr');

        $total = $client->getCrawler()->filter('#total')->first()->attr('data-total');
        $taxes = $client->getCrawler()->filter('#taxes')->first()->attr('data-taxes');
        $final = $client->getCrawler()->filter('#final')->first()->attr('data-final');

        $productQuantity = $line->attr('data-quantity');
        $productPrice = $line->attr('data-price');
        $productTax = $line->attr('data-tax');

        self::assertEquals($total, $totalProduct = $productPrice * $productQuantity);
        self::assertEquals($taxes, $totalProduct * ($productTax / 100));
        self::assertEquals($final, $totalProduct * ($productTax / 100) + $totalProduct);
        self::assertEquals(1, $line->count());
        self::assertEquals(10, $productQuantity);
    }

    public function testRemoveLinePuttingZeroQuantity(): void
    {
        $client = $this->login(parent::LOGIN_GOD);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        $line = [
            'product' => $this->getRepository(Product::class)->findOneBy(['name' => 'The Product']),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['purchase_line[product]']->setValue($line['product']->getId());

        $client->submit($form);

        self::assertEquals(1, $client->getCrawler()->filter('.purchase-line-tr')->count());

        $url = $client->getCrawler()->filter('.change-quantity')->first()->attr('data-href');
        $url = str_replace('value', 0, $url);

        $client->request('GET', $url);

        $line = $client->getCrawler()->filter('.purchase-line-tr');

        $total = $client->getCrawler()->filter('#total')->first()->attr('data-total');
        $taxes = $client->getCrawler()->filter('#taxes')->first()->attr('data-taxes');
        $final = $client->getCrawler()->filter('#final')->first()->attr('data-final');

        self::assertEquals(0, $total);
        self::assertEquals(0, $taxes);
        self::assertEquals(0, $final);
        self::assertEquals(0, $line->count());
    }
}
