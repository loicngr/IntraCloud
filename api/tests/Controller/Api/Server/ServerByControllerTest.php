<?php

namespace App\Tests\Controller\Api\Server;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ServerByControllerTest extends WebTestCase
{
    public function testServerByID()
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
            '/server/1',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => array(
                'id' => 1,
                'adresse' => '127.0.0.0',
                'port' => '3000'
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Get Server by ID
        $client->request(
            'GET',
            '/server/666',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 404,
            "data" => 'server not found.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }
}
