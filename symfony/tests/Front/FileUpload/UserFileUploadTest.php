<?php


namespace App\Tests\Front\FileUpload;


use App\Entity\Client;
use App\Entity\Company;
use App\Entity\MediaObject;
use App\Entity\User;
use App\Tests\Front\BaseTest;

class UserFileUploadTest extends BaseTest
{
    public function testCreateClientWithAvatar(): void
    {
        $client = $this->login(parent::LOGIN_GOD);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'user']));

        $user = [
            'name' => 'Another Test User',
            'email' => 'fake2@email.com',
            'image' => $this->getFile('logo.png','avatar.png'),
            'profile' => User::PROFILE_ADMIN,
            'password' => 'password',
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['user[name]']->setValue($user['name']);
        $form['user[email]']->setValue($user['email']);
        $form['user[profile]']->setValue($user['profile']);
        $form['user[password]']->setValue($user['password']);
        $form['user[image]']->setValue($user['image']);


        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el usuario Another Test User correctamente.',
            trim($successLabel->html())
        );

        $user = $this->getRepository(User::class)->findOneBy(['email' => 'fake2@email.com']);

        self::assertInstanceOf(User::class,$user);
        //self::assertInstanceOf(MediaObject::class, $user->getImage());
        self::assertEquals(1,$client->getCrawler()->filter('img#avatar')->count());
    }
}