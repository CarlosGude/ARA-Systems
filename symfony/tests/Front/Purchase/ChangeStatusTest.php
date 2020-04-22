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
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/purchase/reference');

        $incomingButton = $crawler->filter('.incoming');

        $url = $incomingButton->first()->attr('data-href');
        $client->request('POST', $url);

        $count = $client->getCrawler()->filter('.status-incoming')->count();

        self::assertEquals(1, $count);

        $id = explode('/', $url)[3];
        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        self::assertEquals(Purchase::STATUS_INCOMING, $purchase->getStatus());
    }

    public function testChangeStatusToCancelled(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/purchase/reference');

        $incomingButton = $crawler->filter('.cancelled');

        self::assertEquals(2, $incomingButton->count());

        $url = $incomingButton->first()->attr('data-href');
        $client->request('POST', $url);

        $count = $client->getCrawler()->filter('.status-cancelled')->count();

        self::assertEquals(1, $count);

        $id = explode('/', $url)[3];
        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        self::assertEquals(Purchase::STATUS_CANCELLED, $purchase->getStatus());
    }

    public function testChangeStatusToSuccessAndVerifiedUpdateStock(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/purchase/reference');

        $incomingButton = $crawler->filter('.success');

        self::assertEquals(2, $incomingButton->count());

        $url = $incomingButton->first()->attr('data-href');

        $id = explode('/', $url)[3];

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        $stockBefore = [];
        foreach ($purchase->getPurchaseLines() as $line) {
            $stockBefore[$line->getProduct()->getId()] = [
                'stockAct' => $line->getProduct()->getStockAct(),
                'quantity' => $line->getQuantity(),
            ];
        }

        $client->request('POST', $url);

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->find($id);

        $stockAfter = [];
        foreach ($purchase->getPurchaseLines() as $afterLine) {
            $stockAfter[$afterLine->getProduct()->getId()] = [
                'stockAct' => $afterLine->getProduct()->getStockAct(),
            ];
        }

        foreach ($stockAfter as $id => $stock) {
            self::assertEquals($stock['stockAct'], $stockBefore[$id]['stockAct'] + $stockBefore[$id]['quantity']);
        }
    }
}
