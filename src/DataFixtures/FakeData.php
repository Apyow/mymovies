<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Movie;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class FakeData extends Fixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager) : void
    {
        $faker      = Factory::create('fr-FR');

// Create users
        $user1 = new User();
        $user2 = new User();
        $password1 = $this->encoder->encodePassword($user1, 'pass');
        $password2 = $this->encoder->encodePassword($user2, 'pass');

        $user1->setEmail('admin@local.com')->setPassword($password1)->setRoles(['ROLE_ADMIN']);
        $user2->setEmail('user@local.com')->setPassword($password2)->setRoles(['ROLE_USER']);
        $manager->persist($user1);
        $manager->persist($user2);
        $manager->flush();

// Create movies        
        $genre      = ['Comédie', 'Action', 'Thriller', 'Animation'];

        for ($i = 1; $i <= 30; $i++) {
            $movie      = new Movie();
            
            $movie->setTmdbId(0)
                ->setTitle($faker->jobTitle())
                ->setReleaseDate($faker->date('Y-m-d', 'now'))
                ->setDuration(mt_rand(90, 180))
                ->setGenre($faker->randomElement($genre))
                ->setSynopsis($faker->paragraphs(4, true))
                ->setRating(mt_rand(0,3))
                ->setStatus(mt_rand(1,5))
                ->setUpdatedAt(new \DateTime());

            $manager->persist($movie);
        }
        $manager->flush();
    }
}