<?php


namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Entity\Company;
use App\Entity\Product;
use App\Entity\Provider;
use App\Entity\User;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Component\Console\Application;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class BaseTest
 * @package App\Tests\Functional
 */
abstract class BaseTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    /**
     *
     */
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

    protected function assertResponseIsSuccessfulAndInJson(): void
    {
        self::assertResponseIsSuccessful();
        self::assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
    }

    protected function getGodUser(): ?User
    {
        /** @var User $user */
        $user = static::$container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['roles' => ['ROLE_GOD']]);

        return $user;
    }

    protected function getCompany($name = 'The Company'): ?Company
    {
        /** @var Company $company */
        $company = static::$container->get('doctrine')
            ->getRepository(Company::class)
            ->findOneBy(['name' => $name]);

        return $company;
    }

    protected function getCategory($name = 'The Category'): ?Category
    {
        /** @var Category $category */
        $category = static::$container->get('doctrine')
            ->getRepository(Category::class)
            ->findOneBy(['name' => $name]);

        return $category;
    }

    protected function getProvider($name = 'The Provider'): ?Provider
    {
        /** @var Provider $provider */
        $provider = static::$container->get('doctrine')
            ->getRepository(Provider::class)
            ->findOneBy(['name' => $name]);

        return $provider;
    }

    protected function getProduct($name = 'The Product'): ?Product
    {
        /** @var Product $product */
        $product = static::$container->get('doctrine')
            ->getRepository(Product::class)
            ->findOneBy(['name' => $name]);

        return $product;
    }
}
