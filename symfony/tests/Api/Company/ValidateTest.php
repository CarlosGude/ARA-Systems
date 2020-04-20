<?php

namespace App\Tests\Api\Company;

use App\Tests\Api\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function tesNameRequired(): void
    {
        $company = ['user' => parent::API.'users/'.$this->getGodUser()->getId()];

        $response = static::createClient()->request(
            'POST',
            parent::API.'companies',
            ['headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],
                'body' => json_encode($company),
            ]
        );
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }
}
