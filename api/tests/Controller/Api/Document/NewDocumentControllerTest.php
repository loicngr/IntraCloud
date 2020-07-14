<?php

namespace App\Tests\Controller\Api\Document;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewDocumentControllerTest extends WebTestCase
{
    public function testNewDocument()
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

        // New Document
        $client->request(
            'POST',
            '/document',
            [
                'name' => 'mon_fichier.txt',
                'location' => '/home/loic/mon_dossier',
                'size' => 300.63,
                'user_id' => 3,
                'server_id' => 2
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => 'document created.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }
}
