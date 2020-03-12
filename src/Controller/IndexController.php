<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\TmdbGenresRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index")
     */
    public function index(MovieRepository $movieRepo, TmdbGenresRepository $genresRepo) : Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        $movies = $movieRepo->findBy(array('status' => array(1, 2)), array('id' => 'DESC'));
      
        if(count($movies) === 0) {
            $this->forward('App\Controller\TMDBController::syncGenre');
            return $this->redirectToRoute('movie_add');
        }

        return $this->render('index.html.twig', [
            'movies'    => $movies,
            'genres'    => $genresRepo->getGenresIdAndName()
        ]);
    }

    /**
     * @Route("/{lang}", name="switch_lang", requirements={"lang":"en|fr"})
     */
    public function switchLang(Request $request, string $lang) : RedirectResponse
    {
        //Get current url to redirect user after language is changed
        $url_to_redirect = $_SERVER['HTTP_REFERER'];

        //Set the locale with parameters in url
        $request->getSession()->set('_locale', $lang);

        return $this->redirect($url_to_redirect);
    }
}
