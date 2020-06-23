<?php

namespace App\Tests\Api\Management\RoleGod;

use App\Entity\Company;
use App\Tests\Api\BaseTest;
use DateTime;
use DateTimeZone;
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
     * @var array
     */
    protected $token;

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->token = $this->getToken();
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadAllCompanies(): void
    {
        $response = static::createClient()->request('GET', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(3, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadACompany(): void
    {
        /** @var Company $company */
        $company = $this->getCompany();

        $response = static::createClient()->request('GET', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $company->getName(),
            $response['name'],
            'The expected name was '.$response['name'].' but '.$company->getName().' has found'
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAnCompany(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode($company),
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $company['name'],
            $response['name'],
            'The expected name was '.$response['name'].' but '.$response['name'].' has found'
        );
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
        /** @var Company $company */
        $company = $this->getCompany();

        $response = static::createClient()->request('PUT', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
            'body' => json_encode(['name' => 'Fake Company']),
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            'Fake Company',
            $response['name'],
            'The expected name was '.$response['name'].' but '.$company->getName().' has found'
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteACompany(): void
    {
        /** @var Company $company */
        $company = $this->getCompany('The Company 3');

        static::createClient()->request('DELETE', parent::API.'companies/'.$company->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token']],
        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
