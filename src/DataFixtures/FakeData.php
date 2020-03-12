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
        $genre      = ['Comedie', 'Action', 'Thriller', 'Animation'];

        for ($i = 1; $i <= 30; $i++) {
            $movie      = new Movie();
            
            $title      = $faker->jobTitle();
            $synopsis   = $faker->paragraphs(4, true);

            $movie->setTmdbId(mt_rand(10000, 40000))
                ->setTitle($title)
                ->setReleaseDate($faker->date('Y-m-d', 'now'))
                ->setDuration(mt_rand(90, 180))
                ->setGenre($faker->randomElement($genre))
                ->setSynopsis($synopsis)
                ->setPoster('')
                ->setRating(mt_rand(0,3))
                ->setStatus(mt_rand(1,5))
                ->setUpdatedAt(new \DateTime());

            $manager->persist($movie);
        }
        $manager->flush();

// Replace default generated poster from slug title
        $sql = 'UPDATE movie set poster="notfound.jpg"';
        $stmt = $manager->getConnection()->prepare($sql); 
        $stmt->execute();
    }
}