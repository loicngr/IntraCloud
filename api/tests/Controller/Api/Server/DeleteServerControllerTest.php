<?php

namespace App\Tests\Controller\Api\Server;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DeleteServerControllerTest extends WebTestCase
{
    public function testDeleteServer()
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

        // Delete Server by ID
        $client->request(
            'DELETE',
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
            "data" => 'server deleted.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Delete Server by ID
        $client->request(
            'DELETE',
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
