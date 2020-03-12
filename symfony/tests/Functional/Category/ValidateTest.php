<?php


namespace App\Tests\Functional\Category;

use App\Tests\Functional\User\ManagementTest;

class ValidateTest extends ManagementTest
{
    public function testUserRequired()
    {
        $category = ['name' => 'test', 'description' => 'test'];

        $response = static::createClient()->request('POST', parent::API . 'categories', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($category)
        ]);
        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'user: This value should not be blank.',
            $response['hydra:description']
        );
    }
}
