<?php

namespace App\Tests\Front\Purchase\RoleSeller;

use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ManipulateQuantityTest extends BaseTest
{
    public function testAddProductToPurchase(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testAddQuantityToLine(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRemoveLinePuttingZeroQuantity(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);
        $purchase = $this->createPurchase('carlos.sgude@gmail.com', 'The Provider', 'test');

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'purchase', 'id' => $purchase->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
