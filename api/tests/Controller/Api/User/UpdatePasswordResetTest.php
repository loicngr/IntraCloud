<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UpdatePasswordResetTest extends WebTestCase
{
    public function testUpdateResetPassword()
    {
        $client = static::createClient();

        $client->request(
            'POST',
            '/user/update/resetpassword/efeacfbde5725c9cc201',
            [
                "password" => "password",
                "email" => "pro.nogierloic@gmail.com"
            ],
            [],
            [
                'CONTENT_TYPE' => 'application/json'
            ]
        );
        dump($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
