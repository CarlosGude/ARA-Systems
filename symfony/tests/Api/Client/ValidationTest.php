<?php


namespace App\Tests\Api\Client;


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
    public function testNameRequired(): void
    {
        $client = [
            'email' => 'fake@email.com',
            'identification'=>'3616884',
            'company' => parent::API.'companies/'.$this->getCompany()->getId()
            ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
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
    public function testIdentificationRequired(): void
    {
        $client = [
            'email' => 'fake@email.com',
            'name'=>'Test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId()
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'identification: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testEmailRequired(): void
    {
        $client = [
            'name' => 'test',
            'identification'=>'3616884',
            'company' => parent::API.'companies/'.$this->getCompany()->getId()
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
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
    public function testEmailUnique(): void
    {
        $client = [
            'name' => 'test',
            'email' => 'client@email.com',
            'identification'=>'3616884',
            'company' => parent::API.'companies/'.$this->getCompany()->getId()
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: Este valor ya se ha utilizado.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testIdentificationUnique(): void
    {
        $client = [
            'name' => 'test',
            'email' => 'fake@email.com',
            'identification'=>'666',
            'company' => parent::API.'companies/'.$this->getCompany()->getId()
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'identification: Este valor ya se ha utilizado.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $client = [
            'name' => 'test',
            'email' => 'fake@email.com',
            'identification'=>'886756135151',
        ];

        $response = static::createClient()->request(
            'POST',
            parent::API.'clients',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($client),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }
}