<?php

namespace App\DataFixtures;

use Faker;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    protected $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($i = 0; $i < 10; $i++):
            $user = new User();

            $user
                ->setEmail($i == 2 ? 'admin@email.fr' : $faker->email)
                ->setFirstName($faker->firstName)
                ->setLastName($faker->lastName)
                ->setCreatedAt(new \DateTime())
                ->setRoles($i == 2 ? ['ROLE_ADMIN'] : ['ROLE_NOT_VERIFIED'])
                ->setPassword($this->encoder->encodePassword($user, 'password'));

            $manager->persist($user);
        endfor;

        $manager->flush();
    }
}
