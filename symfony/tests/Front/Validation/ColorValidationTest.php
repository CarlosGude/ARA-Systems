<?php

namespace App\Tests\Front\Validation;

use App\Tests\Front\BaseTest;

class ColorValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'color']));

        $form = $crawler->selectButton('Guardar')->form();

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }
}
