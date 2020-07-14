<?php

namespace App\Tests\Controller\Api\Document;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DocumentByControllerTest extends WebTestCase
{
    public function testDocumentByID()
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

        // Get Document by ID
        $client->request(
            'GET',
            '/document/1',
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

    public function testDocumentBySearch()
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

        // Get Document by filename and location
        $client->request(
            'GET',
            '/document/search/q',
            [
                'name' => 'mon_fichier.txt',
                'location' => '/home/loic/mon_dossier',
                'server_id' => 2
            ],
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
