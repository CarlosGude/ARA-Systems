<?php

namespace App\Tests\Front\Validation;

use App\Entity\Company;
use App\Entity\MediaObject;
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

    public function testLogoShouldBeAnImage(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));
        $company = [
            'name' => 'Test Company',
            'logo' => $this->createFile('document.pdf','company.pdf')
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[logo]']->setValue($company['logo']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('El archivo no es una imagen válida.', $errorSpan->html());
    }
}
