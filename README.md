# My Movies

Cette app est une vidéothèque développée sous Symfony 5.0.5 et utilisant [l'API TMDB v3](https://developers.themoviedb.org/3) :
* Recherche d'un film, récupération des données associées...
* Tri de la collection avec [Isotope](https://isotope.metafizzy.co).
* Films en cours reprenant les films à venir etc...
* L'admin accède à tout dont entres autres une page de conf TMDB avec :
  * Une vérification/synchro des genres dispos sur TMDB avec la db de l'app.
  * Le détail de la conf de l'API (`api.themoviedb.org/3/configuration`)
* L'utilisateur n'accède qu'à la collection (page d'accueil).

## Pré-requis
Avoir un compte sur [TMDB](https://www.themoviedb.org/) pour récupérer la clé API dans les [paramètres du compte](https://www.themoviedb.org/settings/api).

## Installation

* git clone puis composer install.
* ajout de la **clé de l'API TMDB** dans le .env + les infos de connexion à la DB en utilisant le .env.dist comme modèle.
* les classiques de Symfony pour créer la db avec les infos du .env :   
```console
doctrine:database:create
doctrine:schema:create
```
* créer l'utilisateur admin en chargeant la fixture d'init pour l'env de dev ou en jouant InitOnly.sql présent dans DataFixtures pour la prod :

```
doctrine:fixtures:load --group=InitOnly
```

* L'admin peut ensuite créer des comptes utilisateurs.

---
## Divers
Des données de tests peuvent être chargées (`doctrine:fixtures:load --group=FakeData`) pour générer : 
* des faux films 
* 2 utilisateurs : admin@local.com et user@local.com avec "pass" comme mot de passe
---

