<?php

namespace App\Controller;

use App\Entity\TmdbGenres;
use App\Repository\TmdbGenresRepository;
use App\Service\TmdbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TMDBController extends AbstractController
{
    /**
     * @Route("/tmdb", name="tmdb_config")
     */
    public function index(TmdbService $tmdb, TmdbGenresRepository $repo)
    {
        return $this->render('tmdb/index.html.twig', [
            'tmdbconfig'    => $tmdb->getConfig(),
            'last_sync'     => $repo->getLastSync(),
            'synced'        => $repo->getGenresIdAndName() === $tmdb->getGenresIdAndName()
        ]);
    }

    /**
     * @Route("/tmdb/sync-genres", name="tmdb_sync_genres")
     */
    public function syncGenre(EntityManagerInterface $em, TmdbService $tmdb){
        $connection = $em->getConnection();
        $platform   = $connection->getDatabasePlatform();
          
        $connection->executeUpdate($platform->getTruncateTableSQL('tmdb_genres', true));

        $g = $tmdb->getGenresIdAndName();

        foreach ($g as $key => $value){
            $genres  = new TmdbGenres();
            $genres->setTmdbId($key)->setName($value);
            $em->persist($genres);
        }

        $em->flush();

        return $this->redirectToRoute('tmdb_config');
    }
}
