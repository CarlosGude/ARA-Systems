<?php

namespace App\Tests\Front\Security;

use App\Tests\Front\BaseTest;

class LoginTest extends BaseTest
{
    public function testSuccessLogin(): void
    {
        $this->login(parent::LOGIN_GOD);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Hola, carlos.sgude@gmail.com.');
    }

    public function testFailEmailLogin(): void
    {
        $client = $this->login(['email' => 'fake@gmail.com', 'password' => 'fake']);

        $errorSpan = $client->getCrawler()->filter('.alert-danger')->first();

        self::assertEquals('Email no reconocido.', $errorSpan->html());
    }

    public function testFailPasswordLogin(): void
    {
        $client = $this->login(['email' => 'carlos.sgude@gmail.com', 'password' => 'fail']);

        $errorSpan = $client->getCrawler()->filter('.alert-danger')->first();

        self::assertEquals('Credenciales no válidas.', $errorSpan->html());
    }
}
