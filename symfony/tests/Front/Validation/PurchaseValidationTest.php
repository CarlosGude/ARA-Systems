<?php

namespace App\Tests\Front\Validation;

use App\Entity\MediaObject;
use App\Entity\Provider;
use App\Tests\Front\BaseTest;

class PurchaseValidationTest extends BaseTest
{

    public function testTheLogoMustBeAnImage(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'provider']));

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake2@gmail.com',
            'image' => $this->getFile('document.pdf','company.pdf'),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);
        $form['provider[email]']->setValue($provider['email']);
        $form['provider[image]']->setValue($provider['image']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#avatar')->count());
        self::assertEquals('El archivo no es una imagen válida.', $errorSpan->html());
    }
}
