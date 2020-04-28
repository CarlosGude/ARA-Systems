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
            'logo' => $this->createFile('logo.png','company.png')
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[logo]']->setValue($company['logo']);

        $client->submit($form);

        /** @var Company $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'Test Company']);

        $crawler = $client->request('GET', $this->generatePath('front_edit', [
            'entity' => 'company',
            'id' => $company->getId()
        ]));

        self::assertInstanceOf(Company::class,$company);
        self::assertInstanceOf(MediaObject::class, $company->getImage());
        self::assertEquals(1,$crawler->filter('img#logo')->count());
    }
}