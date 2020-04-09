<?php

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
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

    protected const API = '/api/v1/';

    /**
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

    /**
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

    /**
     * @return User|null
     */
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
     *
     * @return User|null
     */
    protected function getUserByEmail(string $email): ?User
    {
        /** @var User $user */
        $user = static::$container->get('doctrine')
            ->getRepository(User::class)
            ->findOneBy(['email' => $email]);

        return $user;
    }

    /**
     * @return Company|null
     */
    protected function getCompany(string $name = 'The Company'): ?Company
    {
        /** @var Company $company */
        $company = static::$container->get('doctrine')
            ->getRepository(Company::class)
            ->findOneBy(['name' => $name]);

        return $company;
    }

    /**
     * @return Category|null
     */
    protected function getCategory(string $name = 'The Category'): ?Category
    {
        /** @var Category $category */
        $category = static::$container->get('doctrine')
            ->getRepository(Category::class)
            ->findOneBy(['name' => $name]);

        return $category;
    }

    /**
     * @return Provider|null
     */
    protected function getProvider(string $name = 'The Provider'): ?Provider
    {
        /** @var Provider $provider */
        $provider = static::$container->get('doctrine')
            ->getRepository(Provider::class)
            ->findOneBy(['name' => $name]);

        return $provider;
    }

    /**
     * @return Product|null
     */
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
     * @param string $reference
     *
     * @return Purchase|null
     */
    protected function getPurchase(Company $company = null, $reference = 'reference'): ?Purchase
    {
        (!$company && $company = $this->getCompany());

        /** @var Purchase $purchase */
        $purchase = static::$container->get('doctrine')
            ->getRepository(Purchase::class)
            ->findOneBy([
                'company' => $company,
                'reference' => $reference,
            ]);

        return $purchase;
    }

    /**
     * @param string $reference
     *
     * @return PurchaseLine|null
     */
    protected function getPurchaseLine(Product $product, $reference = 'reference'): ?PurchaseLine
    {
        $purchase = $this->getPurchase($product->getCompany(), $reference);

        /** @var PurchaseLine $purchaseLine */
        $purchaseLine = static::$container->get('doctrine')
            ->getRepository(PurchaseLine::class)
            ->findOneBy([
                'product' => $product,
                'purchase' => $purchase,
            ]);

        return $purchaseLine;
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
