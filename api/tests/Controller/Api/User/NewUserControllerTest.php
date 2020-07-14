<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NewUserControllerTest extends WebTestCase
{
    public function testNewUser()
    {
        $client = static::createClient();

        // Create User Account
        $client->request(
            'POST',
            '/user',
            [
                'firstname' => 'Fabien',
                'lastname' => 'Fabien',
                'email' => 'Fabien5@email.fr',
                'password' => 'password'
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ]
        );
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
