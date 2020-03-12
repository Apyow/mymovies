<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\TmdbService;
use App\Repository\MovieRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/")
 */
class MovieController extends AbstractController
{
    /**
     * @Route("/movies", name="movies_index", methods="GET")
     */
    public function index(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/movies.html.twig', ['movies' => $movieRepository->findBy(array('status' => array(1, 2)), array('updatedAt' => 'DESC'))]);
    }

    /**
     * @Route("/movies/inprogress", name="movies_inprogress")
     */
    public function inprogress(MovieRepository $movieRepository): Response
    {
        return $this->render('movie/inprogress.html.twig', ['moviesInprogress' => $movieRepository->findBy(array('status' => array(3, 4, 5)), array('releaseDate' => 'DESC'))]);
    }

    /**
     * @Route("/movie/{id}", name="movie_show", methods="GET")
     */
    public function show(Movie $movie): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->render('movie/single.html.twig', ['movie' => $movie]);
    }

    /**
     * @Route("/movie/edit/{id}", name="movie_edit", methods="GET|POST")
     */
    public function edit(Request $request, Movie $movie): Response
    {
        $form = $this->createForm(MovieType::class, $movie);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirect($request->request->get('referer'));
        }

        return $this->render('movie/edit.html.twig', [
            'referer' => array_key_exists('HTTP_REFERER', $_SERVER ) ? $_SERVER['HTTP_REFERER'] : "/",
            'movie' => $movie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/movie/sync/{tmdbId}", name="movie_sync", methods="GET|POST")
     */
    public function sync(int $tmdbId, TmdbService $tmdb, Movie $movie)
    {
        $localData = array(
            $movie->getTitle(),
            $movie->getTmdbPoster(),
            $movie->getReleaseDate(),
            $movie->getDuration(),
            $movie->getGenre(),
            $movie->getSynopsis(),
        );

        $tmdbData = $tmdb->getData($tmdbId);
        $lessTmdbData = array(
            "movie_title" => $tmdbData['title'],
            "movie_tmdbPoster" => $tmdbData['poster_path'],
            "movie_releaseDate" => $tmdbData['release_date'],
            "movie_duration" => $tmdbData['runtime'],
            "movie_genre" => $tmdbData['genres'],
            "movie_synopsis" => $tmdbData['overview']
        );

        return new JsonResponse(array_diff($lessTmdbData, $localData),200);
    }

    /**
     * @Route("/movie/delete/{id}", name="movie_delete", methods="DELETE")
     */
    public function delete(Request $request, Movie $movie): Response
    {
        if ($this->isCsrfTokenValid('delete'.$movie->getId(), $request->request->get('_token'))) {
            $filename = $movie->getTmdbId().'.jpg';
            (new Filesystem)->remove([$this->getParameter('postersDir').'/'.$filename, $this->getParameter('postersThumbs').'/'.$filename]);
            
            $em = $this->getDoctrine()->getManager();
            $em->remove($movie);
            $em->flush();
        }

        return $this->redirectToRoute('movies_index');
    }

}
