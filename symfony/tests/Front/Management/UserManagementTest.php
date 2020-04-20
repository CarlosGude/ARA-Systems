<?php


namespace App\Tests\Front\Management;


use App\Entity\User;
use App\Tests\Front\BaseTest;
use Symfony\Component\DomCrawler\Form;

class UserManagementTest extends BaseTest
{
    public function testListUsers(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/user');

        $count = $crawler->filter('.user-tr')->count();
        $total = $crawler->filter('.table')->first()->attr('data-total');

        self::assertEquals(10, $count);
        self::assertEquals(11, $total);
    }

    public function testCreateUser(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/create/user');

        $user = [
            'name' => 'Test User',
            'email' => 'fake@email.com',
            'password' => 'password'
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[password]']->setValue($user['password']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            trim($errorSpan->html()),
            'Se ha creado el usuario Test User correctamente.'
        );

        $user = $this->getRepository(User::class)->findOneBy(['email' => 'fake@email.com']);

        self::assertNotNull($user);
    }

    public function testUserEdited(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        /** @var User $userToEdit */
        $userToEdit = $this->getRepository(User::class)->findOneBy(['email' => 'fake@email.com']);

        $crawler = $client->request('GET', '/edit/user/'.$userToEdit->getId());

        $user = [
            'name' => 'Test User Edited',
        ];

        /** @var Form $form */
        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);

        $client->submit($form);

        $errorSpan = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            trim($errorSpan->html()),
            'Se ha editado el usuario Test User Edited correctamente.'
        );
    }

    public function testRemoveUsers(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'pasalacabra']);

        $crawler = $client->request('GET', '/list/user');

        $crawler->filter('.delete')->each(static function ($delete) use ($client) {
            $client->request('POST', $delete->attr('data-url'));
        });

        self::assertEquals(0, $client->getCrawler()->filter('.invitation-tr')->count());
    }
}