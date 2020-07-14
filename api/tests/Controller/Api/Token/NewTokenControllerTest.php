<?php

namespace App\Tests\Controller\Api\Token;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewTokenControllerTest extends WebTestCase
{
    public function testNewToken()
    {
        // Generate Token JWT
        $client = static::createClient();
        $client->request(
            'POST',
            '/login_check',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ],
            '{"email": "pro.nogierloic@gmail.com", "password": "password"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $token = json_decode($client->getResponse()->getContent())->token;

        // Get token by Id
        $client->request(
            'POST',
            '/token',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
