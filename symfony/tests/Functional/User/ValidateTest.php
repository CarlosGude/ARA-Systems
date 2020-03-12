<?php


namespace App\Tests\Functional\User;

class ValidateTest extends ManagementTest
{
    public function testValidEmail()
    {
        $user = ['email' => 'Wrong email', 'name' => 'test', 'password' => 'test'];

        $response = static::createClient()->request('POST', parent::API . 'users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($user)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 204');
        $this->assertEquals(
            'email: This value is not a valid email address.',
            $response['hydra:description']
        );
    }

    public function testEmailIsRequired()
    {
        $user = ['name' => 'test', 'password' => 'test'];

        $response = static::createClient()->request('POST', parent::API . 'users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($user)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'email: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testNameIsRequired()
    {
        $user = ['email' => 'test@email.com', 'password' => 'test'];

        $response = static::createClient()->request('POST', parent::API . 'users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($user)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'name: This value should not be blank.',
            $response['hydra:description']
        );
    }

    public function testPasswordIsRequired()
    {
        $user = ['email' => 'test@email.com', 'name' => 'test'];

        $response = static::createClient()->request('POST', parent::API . 'users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $this->token['token'],
                'Content-Type' => 'application/json'
            ],
            'body' => json_encode($user)
        ]);

        $response = json_decode($response->getBrowserKitResponse()->getContent(), true);

        $this->assertResponseStatusCodeSame(400, 'The response is not 400');
        $this->assertEquals(
            'password: This value should not be blank.',
            $response['hydra:description']
        );
    }

}
