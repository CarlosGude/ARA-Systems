<?php

namespace App\Tests\Front\Management\RolePurchaser;

use App\Entity\Category;
use App\Entity\Company;
use App\Tests\Front\BaseTest;
use Symfony\Component\HttpFoundation\Response;

class CompanyManagementTest extends BaseTest
{
    public function testListCompanies(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $client->request('GET', $this->generatePath('front_list', ['entity' => 'company']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCreateCompany(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        $client->request('GET', $this->generatePath('front_create', ['entity' => 'company']));

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testCompanyEdited(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Category $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'Another Company']);

        $client->request(
            'GET',
            $this->generatePath('front_edit', ['entity' => 'company', 'id' => $company->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testRemoveCompany(): void
    {
        $client = $this->login(parent::LOGIN_PURCHASER);

        /** @var Category $company */
        $company = $this->getRepository(Company::class)->findOneBy(['name' => 'The Company 3']);
        $client->request('GET', $this->generatePath('front_list', ['entity' => 'company']));
        $client->request(
            'GET',
            $this->generatePath('front_delete', ['entity' => 'company', 'id' => $company->getId()])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }
}
