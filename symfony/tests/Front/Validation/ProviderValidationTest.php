<?php


namespace App\Tests\Front\Validation;

use App\Entity\Provider;
use App\Tests\Front\BaseTest;

class ProviderValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'provider']));

        $provider = [
            'email' => 'fake@gmail.com'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[email]']->setValue($provider['email']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertNull($provider);
    }

    public function testEmailRequired(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'provider']));

        $provider = [
            'name' => 'Test Provider',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertNull($provider);
    }

    public function testEmailValid(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity'=>'provider']));

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);
        $form['provider[email]']->setValue($provider['email']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no es una dirección de email válida.', $errorSpan->html());

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertNull($provider);
    }
}
