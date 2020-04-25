<?php

namespace App\Tests\Front\Validation;

use App\Tests\Front\BaseTest;

class PurchaseValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'purchase']));

        $provider = ['description' => 'Test description'];

        $form = $crawler->selectButton('Guardar')->form();

        //$form['purchase[description]']->setValue($provider['description']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }
}
