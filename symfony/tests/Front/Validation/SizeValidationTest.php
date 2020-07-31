<?php

namespace App\Tests\Front\Validation;

use App\Entity\Size;
use App\Tests\Front\BaseTest;

class SizeValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'size']));

        $form = $crawler->selectButton('Guardar')->form();

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testTypeValid(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'size']));

        $form = $crawler->selectButton('Guardar')->form();

        $form['size[name]']->setValue('The name');

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }
}
