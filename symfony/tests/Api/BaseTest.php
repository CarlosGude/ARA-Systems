<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Product;
use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\User;
use DateTime;
use DateTimeZone;
use Exception;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class BaseTest.
 */
abstract class BaseTest extends ApiTestCase
{
    use RefreshDatabaseTrait;

    protected const LOGIN_ADMIN = ['email' => 'user@fakemail.com', 'password' => 'thepass'];
    protected const LOGIN_GOD = ['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra'];
    protected const LOGIN_PURCHASER = ['email' => 'purchaser@fakemail.com', 'password' => 'thepass'];
    protected const LOGIN_SELLER = ['email' => 'seller@fakemail.com', 'password' => 'thepass'];

    protected const API = '/api/v1/';

    /**
     * @param string[] $login
     *
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    protected function getToken($login = self::LOGIN_GOD): array
    {
        $response = static::createClient()->request('POST', '/authentication_token', ['json' => $login]);

        return json_decode($response->getContent(), true);
    }

    /**
     * @param DateTime $dateTime
     * @throws Exception
     */
    protected function assertRecentlyDateTime(DateTime $dateTime): void
    {
        $this->assertGreaterThanOrEqual(new DateTime('-2 second', new DateTimeZone('UTC')), $dateTime);
        $this->assertLessThanOrEqual(new DateTime('+2 second', new DateTimeZone('UTC')), $dateTime);
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

    /**
     * @param $email
     */
    protected function getUserByEmail(string $email): ?User
    {
        /** @var User $user */
        $user = static::$container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        return $user;
    }

    protected function getCompany(string $name = 'The Company'): ?Company
    {
        /** @var Company $company */
        $company = static::$container->get('doctrine')
            ->getRepository(Company::class)
            ->findOneBy(['name' => $name]);

        return $company;
    }

    protected function getCategory(string $name = 'The Category', Company $company = null): ?Category
    {
        (!$company && $company = $this->getCompany());
        /** @var Category $category */
        $category = static::$container->get('doctrine')
            ->getRepository(Category::class)
            ->findOneBy(['name' => $name, 'company' => $company]);

        return $category;
    }

    protected function getProvider(string $name = 'The Provider', Company $company = null): ?Provider
    {
        (!$company && $company = $this->getCompany());
        /** @var Provider $provider */
        $provider = static::$container->get('doctrine')
            ->getRepository(Provider::class)
            ->findOneBy(['name' => $name]);

        return $provider;
    }

    protected function getProduct(string $name = 'The Product', Company $company = null): ?Product
    {
        (!$company && $company = $this->getCompany());

        /** @var Product $product */
        $product = static::$container->get('doctrine')
            ->getRepository(Product::class)
            ->findOneBy([
                'name' => $name,
                'company' => $company,
            ]);

        return $product;
    }

    /**
     * @param Company|null $company
     * @param string $status
     * @return Purchase|null
     */
    protected function getPurchase(Company $company = null, string $status = 'pending'): ?Purchase
    {
        (!$company && $company = $this->getCompany());

        /** @var Purchase $purchase */
        $purchase = static::$container->get('doctrine')
            ->getRepository(Purchase::class)
            ->findOneBy([
                'company' => $company,
                'status' => $status,
            ]);

        return $purchase;
    }

    /**
     * @param Company $company
     * @param string $status
     * @return PurchaseLine|null
     */
    protected function getPurchaseLine(Company $company, string $status = 'pending'): ?PurchaseLine
    {
        $purchase = $this->getPurchase($company, $status);

        return $purchase->getPurchaseLines()->first();
    }

    /**
     * @param string $name
     *
     * @param Company|null $company
     * @return PurchaseLine|null
     */
    protected function getPurchaseClient($name = 'The Client', Company $company = null): ?Client
    {
        (!$company && $company = $this->getCompany());

        /** @var Client $client */
        $client = static::$container->get('doctrine')
            ->getRepository(Client::class)
            ->findOneBy([
                'company' => $company,
                'name' => $name,
            ]);

        return $client;
    }

    /**
     * @param $object
     */
    protected function refresh($object): void
    {
        static::$container->get('doctrine')->getManager()->refresh($object);
    }

    /**
     * @param $object
     */
    protected function remove($object): void
    {
        static::$container->get('doctrine')->getManager()->remove($object);
        static::$container->get('doctrine')->getManager()->flush();
    }

    /**
     * @param $object
     */
    protected function persist($object): void
    {
        static::$container->get('doctrine')->getManager()->persist($object);
        static::$container->get('doctrine')->getManager()->flush();
    }
}
