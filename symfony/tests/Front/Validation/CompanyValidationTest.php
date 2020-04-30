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

        $company = [
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testLogoShouldBeAnImage(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
            'logo' => $this->getFile('document.pdf','company.pdf')
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[image]']->setValue($company['logo']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);
        $form['company[image]']->setValue($company['logo']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('El archivo no es una imagen válida.', $errorSpan->html());
    }

    public function testEmailIsRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testPhoneIsRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testAddressIsRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'cif' => '36521478J',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testCifIsRequired(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no debería estar vacío.', $errorSpan->html());
    }

    public function testCifIsValid(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => 'invalid'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no es válido.', $errorSpan->html());
    }

    public function testPhoneHaveDigitsIsValid(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => 'aaaaaaaaa',
            'address' => 'Fake st 123',
            'cif' => 'invalid'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor no es válido.', $errorSpan->html());
    }

    public function testPhoneHaveNineDigits(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        $company = [
            'name' => 'name',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '98745612',
            'address' => 'Fake st 123',
            'cif' => 'invalid'
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);
        $errorSpan = $client->getCrawler()->filter('.form-error-message')->first();

        self::assertEquals(0,$crawler->filter('img#logo')->count());
        self::assertEquals('Este valor debería tener exactamente 9 caracteres.', $errorSpan->html());
    }

}
