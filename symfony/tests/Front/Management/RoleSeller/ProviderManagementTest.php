<?php

namespace App\Tests\Front\Management\RoleSeller;

use App\Entity\Client;
use App\Entity\Provider;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ProviderManagementTest extends BaseTest
{
    public function testListProviders(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'provider']));

        $response = $client->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testCreateProvider(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        $client->request('GET', $this->generatePath('front_create', ['entity' => 'provider']));

        $response = $client->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testProviderEdited(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Provider $providerToEdit */
        $providerToEdit = $this->getRepository(Provider::class)->findOneBy(['name' => 'Another Provider']);

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'provider', 'id' => $providerToEdit->getId()])
        );

        $response = $client->getResponse();

        self::assertEquals(Response::HTTP_FORBIDDEN, $response->getStatusCode());
    }

    public function testRemoveProvider(): void
    {
        $client = $this->login(parent::LOGIN_SELLER);

        /** @var Client $product */
        $product = $this->getRepository(Provider::class)->findOneBy(['name' => 'The Provider']);
        $url = $this->generatePath('front_delete', ['entity' => 'provider', 'id' => $product->getId()]);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
