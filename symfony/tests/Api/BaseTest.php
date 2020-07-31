<?php

namespace App\Tests\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Category;
use App\Entity\Client;
use App\Entity\Color;
use App\Entity\Company;
use App\Entity\Product;
use App\Entity\ProductProvider;
use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\PurchaseLine;
use App\Entity\Size;
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

    protected function getColor(string $name = 'The Color', Company $company = null): ?Color
    {
        (!$company && $company = $this->getCompany());
        /** @var Color $color */
        $color = static::$container->get('doctrine')
            ->getRepository(Color::class)
            ->findOneBy(['name' => $name,'company' => $company]);

        return $color;
    }

    protected function getSizeData(string $name = 'No Size', Company $company = null): ?Size
    {
        (!$company && $company = $this->getCompany());
        /** @var Size $size */
        $size = static::$container->get('doctrine')
            ->getRepository(Size::class)
            ->findOneBy(['name' => $name,'company' => $company]);

        return $size;
    }

    /**
     * @param $email
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

    protected function getProductProvider(
        string $product = 'The Product',
        string $provider = 'The Provider',
        string $company = 'The Company'
    ): ?ProductProvider {

        $company = $this->getCompany($company);
        $product = $this->getProduct($product,$company);
        $provider = $this->getProvider($provider);

        /** @var ProductProvider $productProvider */
        $productProvider = static::$container->get('doctrine')
            ->getRepository(ProductProvider::class)
            ->findOneBy([
                'product' => $product,
                'provider' => $provider,
            ]);

        return $productProvider;
    }

    /**
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
