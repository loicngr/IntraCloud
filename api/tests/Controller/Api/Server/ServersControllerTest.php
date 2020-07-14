<?php

namespace App\Tests\Controller\Api\Server;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServersControllerTest extends WebTestCase
{
    public function testServers()
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

        // Get Server by ID
        $client->request(
            'GET',
            '/servers',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(200, json_decode($client->getResponse()->getContent())->status);
    }
}
