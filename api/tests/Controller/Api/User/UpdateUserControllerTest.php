<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdateUserControllerTest extends WebTestCase
{
    public function testUpdateUser()
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

        // Update User Email
        $client->request(
            'POST',
            '/user/update/5',
            [
                'email' => 'test8@email.fr'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => array("email updated.")
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Update User Email and firstname
        $client->request(
            'POST',
            '/user/update/5',
            [
                'email' => 'test1@email.fr',
                'firstname' => 'loic'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => array("firstname updated.", "email updated.")
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Update User Email and firstname and lastname
        $client->request(
            'POST',
            '/user/update/1',
            [
                'email' => 'test2@email.fr',
                'firstname' => 'loic',
                'lastname' => 'nogier'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => array("firstname updated.", "lastname updated.", "email updated.")
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Update User Email and firstname and lastname and Password
        $client->request(
            'POST',
            '/user/update/6',
            [
                'email' => 'test9@email.fr',
                'firstname' => 'loic',
                'lastname' => 'nogier',
                'password' => 'password5'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );

        $expectedResponse = array(
            "status" => 200,
            "data" => array(
                "password updated.",
                "firstname updated.",
                "lastname updated.",
                "email updated."
            )
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());

        // Update User Role
        $client->request(
            'POST',
            '/user/update/2',
            [
                'role' => 'ROLE_ADMIN'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => array("role updated.")
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }

    public function testSetSerifiedUser()
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

        $client->request(
            'POST',
            '/user/verified/4',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token
            ]
        );
        $expectedResponse = array(
            "status" => 200,
            "data" => 'user updated.'
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(json_encode($expectedResponse), $client->getResponse()->getContent());
    }
}
