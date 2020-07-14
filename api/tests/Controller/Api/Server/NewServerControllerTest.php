<?php

namespace App\Tests\Controller\Api\Server;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewServerControllerTest extends WebTestCase
{
    public function testNewServer()
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

        // New Server
        $client->request(
            'POST',
            '/server',
            [
                'adresse' => '127.0.0.7',
                'port' => '3007',
                'username' => 'root',
                'password' => 'password',
                'privateKey' => 'my_supra_long_private_key_content'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => 'server created.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Test re-create same New Server
        $client->request(
            'POST',
            '/server',
            [
                'adresse' => '127.0.0.7',
                'port' => '3007',
                'username' => 'root',
                'password' => 'password'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 404,
            "data" => 'server already created.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }
}
