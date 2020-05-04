<?php

namespace App\Tests\Front\Management\RolePurchaser;

use App\Entity\Client;
use App\Entity\Provider;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class ProviderManagementTest extends BaseTest
{
    public function testListProviders(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'provider']));

        $count = $crawler->filter('.provider-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(10, $total);
    }

    public function testCreateProvider(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'provider']));

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake2@gmail.com',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);
        $form['provider[email]']->setValue($provider['email']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el proveedor Test Provider correctamente.',
            trim($successLabel->html())
        );

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertNotNull($provider);
    }

    public function testProviderEdited(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Provider $providerToEdit */
        $providerToEdit = $this->getRepository(Provider::class)->findOneBy(['name' => 'Another Provider']);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'provider', 'id' => $providerToEdit->getId()])
        );

        $provider = [
            'name' => 'Test provider updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado el proveedor Test provider updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveProvider(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Client $product */
        $product = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);
        $url = $this->generatePath('front_delete', ['entity' => 'provider', 'id' => $product->getId()]);
        $client->request('GET', $url);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
