<?php

namespace App\Tests\Controller\Api\Server;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateServerControllerTest extends WebTestCase
{
    public function testUpdateServer()
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

        // Update Server
        $client->request(
            'POST',
            '/server/5/update',
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
            "data" => 'server updated.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        //  update fail Server
        $client->request(
            'POST',
            '/server/666/update',
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
            "status" => 404,
            "data" => 'server no found.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }
}
