<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\TmdbService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AddController extends AbstractController
{
    /**
     * @Route("/movie/search", name="movie_code_search")
     */
    public function searchMovies(Request $request, TmdbService $tmdb) : JsonResponse 
    {
        $movieSubmitted = $request->request->get('ajaxsearch');
        return $tmdb->searchMovie($movieSubmitted);
    }

    /**
     * @Route("/movie/add", name="movie_add")
     */
    public function addMovie(Request $request, EntityManagerInterface $em, TmdbService $tmdb, TranslatorInterface $translator) : Response
    {

        $tmdbMovieData = $posters = [];
        $code = $request->query->get('code');
        if (isset($code)) {
            $tmdbMovieData = $tmdb->getData($code);
            $posters       = $tmdb->getPosters($tmdbMovieData['poster_path']);
        }

        $movie  = new Movie();
        $form   = $this->createForm(MovieType::class, $movie)->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            //$post = $request->request->get('movie');
            // $posterName = $post['tmdbId'] . '.jpg';
            // $tmdb->downloadFile($posters['big'], $this->getParameter('postersDir'), $posterName);
            // $tmdb->downloadFile($posters['small'], $this->getParameter('postersThumbs'), $posterName);

            $em->persist($movie);
            $em->flush();

            $this->addFlash(
                'success',
                $translator->trans('text_movie_created')
            );

            return $this->redirectToRoute('movie_add');
        }

        return $this->render('movie/add.html.twig', [
            'posters'   => $posters,
            'movie'     => $tmdbMovieData,
            'form'      => $form->createView(),
        ]);
    }

}
