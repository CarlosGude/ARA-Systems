<?php

namespace App\Tests\Front\Roles\RoleGod;

use App\Entity\Category;
use App\Entity\Company;
use App\Tests\Front\BaseTest;

class CompanyManagementTest extends BaseTest
{
    public function testListCompanies(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_list', ['entity' => 'company']));
        $count = $crawler->filter('.company-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(3, $count);
        self::assertEquals(3, $total);
    }

    public function testCreateCompany(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));
        $company = [
            'name' => 'Test Company',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);
        $form['company[description]']->setValue($company['description']);
        $form['company[email]']->setValue($company['email']);
        $form['company[phone]']->setValue($company['phone']);
        $form['company[address]']->setValue($company['address']);
        $form['company[cif]']->setValue($company['cif']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado la empresa Test Company correctamente.',
            trim($successLabel->html())
        );

        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'Test Company']);

        self::assertNotNull($company);
    }

    public function testCompanyEdited(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Category $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'The Company']);

        $crawler = $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'company', 'id' => $company->getId()])
        );

        $company = [
            'name' => 'Test Company Updated',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['company[name]']->setValue($company['name']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha editado la empresa Test Company Updated correctamente.',
            trim($successLabel->html())
        );
    }

    public function testRemoveCompany(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        /** @var Category $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'The Company 3']);
        $client->request('GET', $this->generatePath('front_list', ['entity' => 'company']));
        $client->request(
            'GET',
            $this->generatePath('front_delete', ['entity' => 'company', 'id' => $company->getId()])
        );

        self::assertEquals(3, $client->getCrawler()->filter('.company-tr')->count());
    }
}
