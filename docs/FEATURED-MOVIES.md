# Films Mis En Avant - Implémentation

## Vue d'ensemble
La fonctionnalité "Films mis en avant" permet d'afficher une section spéciale sur la page d'accueil avec les films recommandés ou populaires marqués comme "featured" dans la base de données.

## Architecture implémentée

### 1. Base de données (SAE2_03.sql)
- **Modification table Movie** : Ajout colonne `featured` (TINYINT(1), DEFAULT 0)
  - 0 = film normal
  - 1 = film mis en avant

### 2. Backend (PHP)

#### server/model.php
- **Fonction `getFeaturedMovies($age = 0)`**
  - Récupère tous les films marqués comme featured
  - Respecte les restrictions d'âge du profil
  - Retourne un tableau d'objets films avec toutes les infos

#### server/controller.php
- **Fonction `readFeaturedController()`**
  - Récupère le paramètre `age` de la requête
  - Appelle `getFeaturedMovies()`
  - Retourne les données pour l'API

#### server/script.php
- **Route `readfeatured`**
  - Endpoint : `/server/script.php?todo=readfeatured&age={age}`
  - Retourne JSON avec les films featured

### 3. Frontend (JavaScript)

#### app/data/dataFeatured.js
- **Module `DataFeatured`**
  - Méthode `read(age)` : Appel API pour récupérer les films featured

#### app/component/FeaturedMovies/
- **script.js**
  - Composant `FeaturedMovies` avec méthode `format()`
  - Affiche la liste des films featured
  - Affiche "Aucun film mis en avant pour le moment." si liste vide
  - Utilise le composant Movie pour chaque film
  
- **template.html**
  - Structure HTML avec header et conteneur
  
- **style.css**
  - Styling cohérent avec le reste du site
  - Grille d'affichage des films
  - Gradient de fond subtil

#### app/index.html
- **Imports** : DataFeatured et FeaturedMovies
- **Variable globale** : `window.featured = []`
- **Fonction `V.loadFeatured()`** : Charge les films featured selon l'âge du profil
- **Fonction `V.renderMovies()`** : 
  - Charge les films featured au début
  - Affiche la section "Films mis en avant" en premier
  - Puis les favoris (si existants)
  - Puis les catégories de films

## Critères d'acceptation - Vérifié ✓

1. **Section "Films mis en avant" visible** ✓
   - Affichée en haut de la page d'accueil
   - Avant les catégories et favoris

2. **Films featured incluent minimum :** ✓
   - Titre ✓
   - Affiche/image ✓
   - Description/synopsis ✓ (via composant Movie existant)

3. **Films marqués comme featured en DB** ✓
   - Colonne `featured` ajoutée à la table Movie

4. **Message si aucun film featured** ✓
   - "Aucun film mis en avant pour le moment."

## Utilisation

### Pour ajouter un film en avant
1. Modifier la base de données : `UPDATE Movie SET featured = 1 WHERE id = {movie_id}`
2. Le film apparaîtra dans la section "Films mis en avant" au prochain chargement

### Pour retirer un film de l'avant
1. Modifier la base de données : `UPDATE Movie SET featured = 0 WHERE id = {movie_id}`

## Exemple API

```
GET /server/script.php?todo=readfeatured&age=12

Response:
[
  {
    "id": 7,
    "title": "Interstellar",
    "year": 2014,
    "length": 169,
    "description": "Un groupe d'explorateurs voyage à travers un trou de mer pour sauver l'humanité.",
    "director": "Christopher Nolan",
    "poster": "interstellar.jpg",
    "trailer": "https://www.youtube.com/embed/VaOijhK3CRU?si=76Ke4uw4LYjuLuQ6",
    "min_age": 12,
    "category": "Science-fiction"
  },
  ...
]
```

## Respect des contraintes architecturales

- ✓ Séparation 3-tier (DB → Controller → Frontend)
- ✓ PDO prepared statements (sécurité SQL)
- ✓ Modulation des composants (réutilisation du composant Movie)
- ✓ Cohérence de style avec le reste du site
- ✓ Respect des restrictions d'âge
