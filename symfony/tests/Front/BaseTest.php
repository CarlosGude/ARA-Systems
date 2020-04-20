<?php


namespace App\Tests\Front;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;

abstract class BaseTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();
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

        $crawler = $client->request('GET', '/');

        /** @var Form $form */
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
}