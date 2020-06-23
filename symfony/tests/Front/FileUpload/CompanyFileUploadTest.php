<?php

namespace App\Tests\Front\FileUpload;

use App\Entity\Company;
use App\Entity\MediaObject;
use App\Tests\Front\BaseTest;

class CompanyFileUploadTest extends BaseTest
{
    public function testCreateCompanyWithLogo(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));
        $company = [
            'name' => 'Test Company',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '987456321',
            'address' => 'Fake st 123',
            'cif' => '32145678J',
            'image' => $this->getFile('logo.png', 'company.png'),
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);
        $form['company[image]']->setValue($company['image']);

        $client->submit($form);

        /** @var Company $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'Test Company']);

        $crawler = $client->request('GET', $this->generatePath('front_edit', [
            'entity' => 'company',
            'id' => $company->getId(),
        ]));

        self::assertInstanceOf(Company::class, $company);
        self::assertEquals(1, $crawler->filter('img#logo')->count());
        self::assertInstanceOf(MediaObject::class, $company->getImage());
    }
}
