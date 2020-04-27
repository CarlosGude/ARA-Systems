<?php

namespace App\Tests\Api\Management\RolePurchaser;

use App\Entity\Company;
use App\Tests\Api\BaseTest;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest.
 */
class CompaniesManagementTest extends BaseTest
{
    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllCompanies(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        static::createClient()->request('GET', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadACompany(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Company $company */
        $company = $this->getCompany('Another Company');

        static::createClient()->request('GET', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnCompany(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        $company = ['name' => 'test', 'description' => 'test'];

        static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($company),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditACompany(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Company $company */
        $company = $this->getCompany('Another Company');

        static::createClient()->request('PUT', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Company']),
        ]);

        self::assertResponseStatusCodeSame(400);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testDeleteACompany(): void
    {
        $token = $this->getToken(parent::LOGIN_PURCHASER);
        /** @var Company $company */
        $company = $this->getCompany('Another Company');

        static::createClient()->request('DELETE', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$token['token']],
        ]);

        self::assertResponseStatusCodeSame(400);
    }
}
