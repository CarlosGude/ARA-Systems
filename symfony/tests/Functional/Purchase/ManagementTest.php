<?php


namespace App\Tests\Functional\Purchase;

use App\Entity\Purchase;
use App\Tests\Functional\BaseTest;
use DateTime;
use DateTimeZone;
use Exception;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ManagementTest
 * @package App\Tests\Functional\Purchase
 */
class ManagementTest extends BaseTest
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
    public function testReadAllPurchases(): void
    {
        $response = static::createClient()->request('GET', parent::API.'purchases', [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token']
            ]
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(2, $response['hydra:totalItems']);
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testReadPurchase(): void
    {
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany());

        $response = static::createClient()->request('GET', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token['token']
            ]
        ]);

        $response = json_decode($response->getContent(), true);

        $this->assertResponseIsSuccessfulAndInJson();
        $this->assertEquals(
            $purchase->getReference(),
            $response['reference']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public function testAddAPurchase(): void
    {
        $purchase = [
            'reference' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
            'provider' => parent::API.'providers/'.$this->getProvider()->getId(),
            'status' => 'pending'
        ];

        $response = static::createClient()->request('POST', parent::API.'purchases', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],


            'body' => json_encode($purchase)
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            $purchase['reference'],
            $response['reference']
        );
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws Exception
     */
    public function testEditAPurchase(): void
    {
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany());

        $response = static::createClient()->request('PUT', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],


            'body' => json_encode([
                'reference' => 'test',
            ])
        ]);
        $this->assertResponseIsSuccessfulAndInJson();
        $response = json_decode($response->getContent(), true);

        $this->assertEquals(
            'test',
            $response['reference']
        );
        $this->assertRecentlyDateTime(new DateTime($response['updatedAt'], new DateTimeZone('UTC')));
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testDeleteAPurchase(): void
    {
        /** @var Purchase $purchase */
        $purchase = $this->getPurchase($this->getCompany());

        static::createClient()->request('DELETE', parent::API.'purchases/'.$purchase->getId(), [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],


        ]);

        self::assertResponseStatusCodeSame(204, 'The response is not 204');
    }
}
