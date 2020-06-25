<?php

namespace App\Tests\Front\Roles\RoleSeller;

use App\Entity\Color;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ColorManagementTest extends BaseTest
{
    public function testListColor(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'color']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateColor(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_create', ['entity' => 'color']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testColorEdited(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Color $color */
        $color = $this->getRepository(Color::class)->findOneBy(['name' => 'The Color']);

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'color', 'id' => $color->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRemoveColor(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Color $color */
        $color = $this->getRepository(Color::class)->findOneBy(['name' => 'The Color 3']);
        $client->request('GET', $this->generatePath('front_list', ['entity' => 'color']));
        $client->request(
            'GET',
            $this->generatePath('front_delete', ['entity' => 'color', 'id' => $color->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
