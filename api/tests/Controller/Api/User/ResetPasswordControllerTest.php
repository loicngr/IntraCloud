<?php

namespace App\Tests\Controller\Api\User;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ResetPasswordControllerTest extends WebTestCase
{
    public function testUserResetPassword()
    {
        $client = static::createClient();

        $client->request('POST', '/user/password/reset', ["email" => "pro.nogierloic@gmail.com"]);
        dump($client->getResponse()->getContent());
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
