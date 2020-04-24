<?php

namespace App\Tests\Front;

use App\Entity\Provider;
use App\Entity\Purchase;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class BaseTest extends WebTestCase
{
    protected const BASE_URL = 'management';

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function generatePath(string $name, array $parameters)
    {
        return static::$container->get('router')->generate($name, $parameters);
    }

    protected function getRepository(string $class): ObjectRepository
    {
        /** @var ManagerRegistry $manager */
        $manager = static::$container->get('doctrine');

        return $manager->getRepository($class);
    }

    protected function login(array $user): KernelBrowser
    {
        $client = static::createClient();
        $client->followRedirects();

        $crawler = $client->request('GET', $this->generatePath('app_login', []));

        $form = $crawler->selectButton('Acceder')->form();

        $form['email']->setValue($user['email']);
        $form['password']->setValue($user['password']);

        $client->submit($form);

        self::assertResponseIsSuccessful();

        return $client;
    }

    /**
     * @param $object
     */
    protected function refresh($object): void
    {
        static::$container->get('doctrine')->getManager()->refresh($object);
    }

    protected function createPurchase(string $email, string $provider, string $reference): Purchase
    {
        $manager = static::$container->get('doctrine')->getManager();
        /** @var User $user */
        $user = $this->getRepository(User::class)->findOneBy(['email' => $email]);

        /** @var Provider $provider */
        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => $provider]);

        $purchase = new Purchase();

        $purchase
            ->setUser($user)
            ->setCompany($user->getCompany())
            ->setProvider($provider)
            ->setReference($reference)
            ;

        $manager->persist($purchase);
        $manager->flush();

        return $purchase;
    }
}
