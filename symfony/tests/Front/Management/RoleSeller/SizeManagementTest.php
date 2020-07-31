<?php

namespace App\Tests\Front\Management\RoleSeller;

use App\Entity\Size;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class SizeManagementTest extends BaseTest
{
    public function testListSize(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'size']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateSize(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_create', ['entity' => 'size']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testSizeEdited(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Size $size */
        $size = $this->getRepository(Size::class)->findOneBy(['name' => 'No Size']);

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'size', 'id' => $size->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRemoveSize(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Size $size */
        $size = $this->getRepository(Size::class)->findOneBy(['name' => 'The Size 3']);
        $client->request('GET', $this->generatePath('front_list', ['entity' => 'size']));
        $client->request(
            'GET',
            $this->generatePath('front_delete', ['entity' => 'size', 'id' => $size->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
