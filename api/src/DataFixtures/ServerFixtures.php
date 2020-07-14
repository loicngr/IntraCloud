<?php

namespace App\DataFixtures;

use App\Entity\Server;
use App\Service\Encrypt\Encryption;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ServerFixtures extends Fixture
{
    protected $encryption;

    public function __construct(Encryption $encryption)
    {
        $this->encryption = $encryption;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++):
            $server = new Server();
            $server
                ->setName("serveur $i")
                ->setAdresse("127.0.0.$i")
				->setAcceptedRoles(['ROLE_USER', 'ROLE_ADMIN'])
                ->setPort("2$i")
                ->setDefaultPath('/var/www/html')
                ->setPassword($this->encryption->encrypt('password'))
                ->setUsername('root');

            $manager->persist($server);
        endfor;

        $manager->flush();
    }
}
