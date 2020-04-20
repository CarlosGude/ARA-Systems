<?php

namespace App\Tests\Api\Provider;

use App\Tests\Api\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest.
 */
class ValidateTest extends ManagementTest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function testNameRequired(): void
    {
        $category = [
            'description' => 'test',
            'company' => parent::API.'companies/'.$this->getCompany()->getId(),
        ];

        $response = static::createClient()->request('POST', parent::API.'categories', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($category),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function testCompanyRequired(): void
    {
        $category = [
            'name' => 'test',
            'description' => 'test',
        ];

        $response = static::createClient()->request('POST', parent::API.'categories', [
            'headers' => ['Authorization' => 'Bearer '.$this->token['token'], 'Content-Type' => 'application/json'],

            'body' => json_encode($category),
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: Este valor no debería estar vacío.',
            $response['hydra:description']
        );
    }
}
