# My Movies

Cette app est une vidéothèque développée sous Symfony 5 et utilisant [l'API TMDB v3](https://developers.themoviedb.org/3) :
* Recherche d'un film, récupération des données associées.
* Tri de la collection avec [Isotope](https://isotope.metafizzy.co).
* Films en cours reprenant les films à venir etc...
* Synchro des données locales avec celle de TMDB depuis l'édition d'un film : Si des différences existent, elles apparaissent au dessus du champ concerné. En cliquant sur cette différence, le champ associé du formulaire est mis à jour pour pouvoir ensuite enregistrer le film avec les modifs choisies.
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
Une liste créée sur TMDB peut être importée en utilisant la v4 de l'api TMDB et en renseignant dans le .env (modèle dans .env.dist) :
* l'id de la liste.
* le token d'accès de l'API v4 (récupérable au même endroit que la clé API citée plus haut).

Ensuite :
* `doctrine:fixtures:load --group=ImportFromTmdbList --append` (append puisque la fixture d'init a été jouée plus haut)

Par défaut elle insère la note personnelle et le statut (HD) à 2.

