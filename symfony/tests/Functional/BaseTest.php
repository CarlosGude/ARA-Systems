<?php


namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\Console\Application;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

abstract class BaseTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected const API = '/api/v1/';
    protected $em;
    /**
     * @var Application
     */
    private $application;

    /**
     * @return array
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken(): array
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => [
            'email' => 'carlos.sgude@gmail.com',
            'password' => 'pasalacabra',
        ]]);

        return json_decode($response->getContent(), true);
    }

    protected function assertResponseIsSuccessfulAndInJson()
    {
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    protected function getUser(): ?User
    {

        return static::$container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(
                ['roles' => ['ROLE_GOD']]
            );


    }
}
