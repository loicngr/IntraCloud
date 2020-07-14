<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserByControllerTest extends WebTestCase
{
    public function testUserByID()
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
            '{"email": "admin@email.fr", "password": "password"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $token = json_decode($client->getResponse()->getContent())->token;

        // get userByID
        $client->request(
            'GET',
            '/user/3',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUserByEMAIL()
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
            '{"email": "admin@email.fr", "password": "password"}'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $token = json_decode($client->getResponse()->getContent())->token;

        // get userByEMAIL
        $client->request(
            'GET',
            '/user/email/admin@email.fr',
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
