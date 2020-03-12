<?php

namespace App\Service;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;

class TmdbService
{
    protected string $apiKey;
    protected string $apiUrl;
    protected string $language;

    public function __construct() {
        $this->apiKey = $_ENV['TMDB_API_KEY'];
        $this->apiUrl = 'https://api.themoviedb.org/3';
        $this->language = 'fr-FR';
	}

    /**
     * Récupère les informations de configuration de l'API TMDB
     *
     * @return string
     */
    public function getConfig() : string
    {
        $curl = json_decode((new CurlHttpClient)
        ->request('GET', "$this->apiUrl/configuration?api_key=$this->apiKey")
        ->getContent(), true);

        return "<pre>".print_r($curl,true)."</pre>";
    }

    /**
     * Récupère les genres (ID et nom) dispos sur TMDB
     *
     * @return string
     */
    public function getGenresIdAndName()
    {
        $curl = json_decode((new CurlHttpClient)
        ->request('GET', "$this->apiUrl/genre/movie/list?api_key=$this->apiKey&language=$this->language")
        ->getContent(), true);

        return array_column($curl['genres'], 'name', 'id');
    }

    /**
     * Recherche un film sur TMDB
     *
     * @param string $query
     * @param string $language
     * @return string JSON
     */
    public function searchMovie($query, $language = "fr-FR") : JsonResponse
    {
        $tmdbCurl = (new CurlHttpClient)->request('GET', "$this->apiUrl/search/movie?api_key=$this->apiKey&language=$language&query=$query");
        $tmdbmovies = json_decode($tmdbCurl->getContent(), true);
        
        return new JsonResponse([$tmdbmovies['results']], 200);
    }

    /**
     * Récupère les données du film sur TMDB
     *
     * @param integer $code
     * @param string $language
     * @return array<string>
     */
    public function getData($code, $language = "fr-FR") : array
    {
        $data = json_decode((new CurlHttpClient)
                        ->request('GET', "$this->apiUrl/movie/$code?api_key=$this->apiKey&language=$language")
                        ->getContent(), true);

        $data = array_merge($data,array('genres' => implode( ", ", array_column($data['genres'], 'name'))));

        return $data;
    }

    /**
     * Récupère le chemin de l'affiche du film grand et petit format 
     *
     * @param string $poster_path
     * @param string $big
     * @param string $small
     * @return array<string>
     */
    public function getPosters($poster_path, $big = "w500", $small = "w185") : array
    {
        $tmdbConfig = json_decode((new CurlHttpClient)
                    ->request('GET', "$this->apiUrl/configuration?api_key=$this->apiKey")
                    ->getContent(), true);

        $posterBig  = $tmdbConfig['images']['secure_base_url'].$big.$poster_path;
        $posterSmall = $tmdbConfig['images']['secure_base_url'].$small.$poster_path;

        return [
            'big'   => $posterBig, 
            'small' => $posterSmall
        ];
    }

    /**
     * Récupère les films d'une liste TMDB
     *
     * @param integer $id
     * @return array<string>
     */
    public function getIdsFromList($id)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.themoviedb.org/4/list/$id?sort_by=title.asc&language=fr-FR&api_key=$this->apiKey",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array('authorization: Bearer '.$_ENV['TMDB_API_V4_TOKEN'], 'content-type: application/json;charset=utf-8') 
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        if ($err) {echo "cURL Error #:" . $err;} /*else {echo $response;}*/

        $aMovies = json_decode($response,true);
        $moviesIds = str_replace('movie:','', array_keys($aMovies['object_ids']));

        return $moviesIds;
    }

    /**
     * Télécharge un fichier à partir d'une URL donnée et 
     * l'enregistre sous le nom donné dans un dossier local
     *  
     * @param string $url
     * @param string $toDir
     * @param string $withName     
     */
    public function downloadFile($url, $toDir, $withName) : bool
    {
        if ($fp_remote = fopen($url, 'rb')) {

            $local_file = $toDir . "/" . $withName;

            if ($fp_local = fopen($local_file, 'wb')) {
                while ($buffer = fread($fp_remote, 8192)) {
                    fwrite($fp_local, $buffer);
                }
                fclose($fp_local);
            } else {
                fclose($fp_remote);
                return false;
            }

            fclose($fp_remote);
            return true;
        } else {
            return false;
        }
    }

}