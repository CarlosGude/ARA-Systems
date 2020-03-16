<?php


namespace App\Tests\Functional\Company;

use App\Tests\Functional\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function tesNameRequired(): void
    {
        $company = ['user' => parent::API . 'users/' . $this->getGodUser()->getId()];

        $response = static::createClient()->request('POST', parent::API . 'companies', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($company)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: This value should not be blank.',
            $response['hydra:description']
        );
    }
}
