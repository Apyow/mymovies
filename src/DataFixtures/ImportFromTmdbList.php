<?php

namespace App\DataFixtures;

use App\Entity\Movie;
use Cocur\Slugify\Slugify;
use App\Service\TmdbService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ImportFromTmdbList extends Fixture
{

    private $tmdb;
    private $params;

    public function __construct(TmdbService $tmdb, ParameterBagInterface $params)
    {
        $this->tmdb = $tmdb;
        $this->params = $params;
    }

    public function load(ObjectManager $manager)
    {
        $moviesIds = $this->tmdb->getIdsFromList($_ENV['TMDB_INIT_LIST']);

        for ($i = 0; $i < count($moviesIds); $i++) {

            $movieData=$this->tmdb->getData($moviesIds[$i]);
            //$posterName = $movieData['id'] . '.jpg';
            
            $movie = new Movie();
            $movie->setTmdbId($movieData['id'])
                ->setTmdbPoster($movieData['poster_path'])
                ->setTitle($movieData['title'])
                ->setReleaseDate($movieData['release_date'])
                ->setDuration($movieData['runtime'])
                ->setGenre($movieData['genres'])
                ->setSynopsis($movieData['overview'])
                ->setSlug((new Slugify)->slugify($movieData['title']))
                ->setRating(2)
                ->setStatus(2);            

            // $posters = $this->tmdb->getPosters($movieData['poster_path']);            
            // $this->tmdb->downloadFile($posters['big'], $this->params->get('postersDir'), $posterName);
            // $this->tmdb->downloadFile($posters['small'], $this->params->get('postersThumbs'), $posterName);

            $manager->persist($movie);
        }
        $manager->flush();
    }
}