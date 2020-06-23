<?php

namespace App\Tests\Api\Validation;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidationTest.
 */
class CompanyValidationTest extends BaseTest
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
     * @throws TransportExceptionInterface
     */
    public function testNameRequired(): void
    {
        $company = [
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
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testEmailRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testPhoneRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'phone: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testAddressRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'address: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCifRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '698745321',
            'address' => 'Fake st 123',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'cif: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testValidEmailRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake',
            'phone' => '698745321',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor no es una dirección de email válida.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testValidPhoneNoLettersRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => 'aaaaaaaaa',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'phone: Este valor no es válido.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testValidPhoneNineDigitsRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '69874521',
            'address' => 'Fake st 123',
            'cif' => '36521478J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'phone: Este valor debería tener exactamente 9 caracteres.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testValidCifRequired(): void
    {
        $company = [
            'name' => 'test',
            'description' => 'test',
            'email' => 'fake@email.com',
            'phone' => '692874521',
            'address' => 'Fake st 123',
            'cif' => '3652148J',
        ];

        $response = static::createClient()->request('POST', parent::API.'companies', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($company),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'cif: Este valor no es válido.',
            $response['hydra:description']
        );
    }
}
