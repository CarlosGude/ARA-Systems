<?php


namespace App\Tests\Functional\Provider;

use App\Tests\Functional\User\ManagementTest;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

/**
 * Class ValidateTest
 * @package App\Tests\Functional\Provider
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
            'company' => parent::API . 'companies/' . $this->getCompany()->getId()
        ];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'name: This value should not be blank.',
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

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        self::assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'company: This value should not be blank.',
            $response['hydra:description']
        );
    }

}
