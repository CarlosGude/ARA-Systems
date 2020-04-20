<?php

namespace App\Tests\Api\Purchase;

use App\Tests\Api\BaseTest;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ValidationTest extends BaseTest
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
    public function testCompanyIsRequired(): void
    {
        $purchase = $this->getPurchaseData();
        unset($purchase['company']);

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    protected function getPurchaseData(): array
    {
        return [
            'reference' => 'test',
            'taxes' => 10,
            'total' => 10,
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'status' => 'pending',
        ];
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testProviderIsRequired(): void
    {
        $purchase = $this->getPurchaseData();
        unset($purchase['provider']);

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'provider: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testReferenceIsRequired(): void
    {
        $purchase = $this->getPurchaseData();
        unset($purchase['reference']);

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'reference: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testTotalShouldNotBeZero(): void
    {
        $purchase = $this->getPurchaseData();
        unset($purchase['reference']);

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'reference: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testStatusShouldBeValid(): void
    {
        $purchase = $this->getPurchaseData();
        $purchase['status'] = 'Invalid status';

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($purchase),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'status: El valor seleccionado no es una opción válida.',
            $response['hydra:description']
        );
    }
}
