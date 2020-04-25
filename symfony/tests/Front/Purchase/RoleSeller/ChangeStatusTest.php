<?php

namespace App\Tests\Front\Roles\RoleSeller;

use App\Entity\Purchase;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ChangeStatusTest extends BaseTest
{
    public function setUp(): void
    {
        parent::setUp();
        passthru(sprintf('php bin/console h:f:l -n --env=test -q'));
    }

    public function testChangeStatusToIncoming(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->findOneBy([
            'reference' => 'reference',
            'company' => $this->getCompany('The Company'),
        ]);

        $url = $this->generatePath(
            'purchase_change_status',
            ['purchase' => $purchase->getId(), 'status' => Purchase::STATUS_INCOMING]
        );
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testChangeStatusToCancelled(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->findOneBy([
            'reference' => 'reference',
            'company' => $this->getCompany('The Company'),
        ]);

        $url = $this->generatePath(
            'purchase_change_status',
            ['purchase' => $purchase->getId(), 'status' => Purchase::STATUS_CANCELLED]
        );
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testChangeStatusToSuccessAndVerifiedUpdateStock(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Purchase $purchase */
        $purchase = $this->getRepository(Purchase::class)->findOneBy([
            'reference' => 'm_l_pur',
            'company' => $this->getCompany('The Company'),
        ]);

        $url = $this->generatePath(
            'purchase_change_status',
            ['purchase' => $purchase->getId(), 'status' => Purchase::STATUS_SUCCESS]
        );
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
