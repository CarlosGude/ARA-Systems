<?php

namespace App\Tests\Front\Purchase;

use App\Entity\Purchase;
use App\Tests\Front\BaseTest;

class ChangeStatusTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        passthru(sprintf('php bin/console h:f:l -n --env=test -q'));
    }

    public function testChangeStatusToIncoming(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_list', ['entity' => 'purchase', 'sort' => 'reference'])
        );

        $incomingButton = $crawler->filter('.incoming');

        $url = $incomingButton->first()->attr('data-href');
        $client->request('POST', $url);

        $count = $client->getCrawler()->filter('.status-incoming')->count();

        self::assertEquals(2, $count);

        $id = explode('/', $url)[4];
        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        self::assertEquals(Purchase::STATUS_INCOMING, $purchase->getStatus());
    }

    public function testChangeStatusToCancelled(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_list', ['entity' => 'purchase', 'sort' => 'reference'])
        );

        $incomingButton = $crawler->filter('.cancelled');

        self::assertEquals(3, $incomingButton->count());

        $url = $incomingButton->first()->attr('data-href');
        $client->request('POST', $url);

        $count = $client->getCrawler()->filter('.status-cancelled')->count();

        self::assertEquals(2, $count);

        $id = explode('/', $url)[4];
        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        self::assertEquals(Purchase::STATUS_CANCELLED, $purchase->getStatus());
    }

    public function testChangeStatusToSuccessAndVerifiedUpdateStock(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_list', ['entity' => 'purchase', 'sort' => 'reference'])
        );

        $incomingButton = $crawler->filter('.success');

        self::assertEquals(3, $incomingButton->count());

        $url = $incomingButton->first()->attr('data-href');

        $id = explode('/', $url)[4];

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        $stockBefore = [];
        foreach ($purchase->getPurchaseLines() as $line) {
            $product = $line->getProduct();
            $stockBefore[$product->getId()] = [
                'stockAct' => $product->getStockAct(),
                'quantity' => $line->getQuantity(),
            ];
        }

        $client->request('POST', $url);

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        $stockAfter = [];
        foreach ($purchase->getPurchaseLines() as $afterLine) {
            $product = $afterLine->getProduct();
            $stockAfter[$product->getId()] = [
                'stockAct' => $product->getStockAct(),
            ];
        }

        foreach ($stockAfter as $id => $stock) {
            self::assertEquals($stock['stockAct'], $stockBefore[$id]['stockAct'] + $stockBefore[$id]['quantity']);
        }
    }
}
