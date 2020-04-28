<?php


namespace App\Tests\Front\FileUpload;

use App\Entity\MediaObject;
use App\Entity\Provider;
use App\Tests\Front\BaseTest;

class ProviderFileUploadTest extends BaseTest
{
    public function testCreateProviderWithLogo(): void
    {
        $client = $this->login(parent::LOGIN_ADMIN);

        $crawler = $client->request('GET', $this->generatePath('front_create', ['entity' => 'provider']));

        $provider = [
            'name' => 'Test Provider',
            'email' => 'fake2@gmail.com',
            'image' => $this->getFile('logo.png','principal.png')
        ];

        $form = $crawler->selectButton('Guardar')->form();

        $form['provider[name]']->setValue($provider['name']);
        $form['provider[email]']->setValue($provider['email']);
        $form['provider[image]']->setValue($provider['image']);

        $client->submit($form);

        $successLabel = $client->getCrawler()->filter('.alert-success')->first();

        self::assertEquals(
            'Se ha creado el proveedor Test Provider correctamente.',
            trim($successLabel->html())
        );

        $provider = $this->getRepository(Provider::class)->findOneBy(['name' => 'Test Provider']);

        self::assertInstanceOf(Provider::class,$provider);
        self::assertInstanceOf(MediaObject::class, $provider->getImage());
        self::assertEquals(1,$client->getCrawler()->filter('img#logo')->count());
    }
}