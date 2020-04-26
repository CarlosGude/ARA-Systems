<?php

namespace App\Tests\Front\Validation;

use App\Entity\Category;
use App\Tests\Front\BaseTest;

class CompanyValidationTest extends BaseTest
{
    public function testNameRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue(null);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }
}
