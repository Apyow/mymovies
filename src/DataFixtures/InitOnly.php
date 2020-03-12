<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class InitOnly extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) : void
    {
        $user = new User();
        $password = $this->encoder->encodePassword($user, 'pass');

        $user->setEmail('admin@local.com')->setPassword($password)->setRoles(['ROLE_ADMIN']);
        $manager->persist($user);
        $manager->flush();
    }
}