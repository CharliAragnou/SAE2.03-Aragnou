# Statistiques de la plateforme - Implémentation

## Vue d'ensemble
La fonctionnalité "Statistiques" permet aux utilisateurs de consulter diverses métriques sur l'utilisation de la plateforme de films, incluant le nombre de profils, les films favoris, etc.

## Architecture implémentée

### 1. Backend (PHP/MySQL)

#### server/model.php
- **Fonction `getTotalProfiles()`**
  - Retourne le nombre total de profils créés
  - `SELECT COUNT(*) as total FROM Profile`

- **Fonction `getAverageFavoritesPerProfile()`**
  - Calcule la moyenne de films favoris par profil
  - Utilise une sous-requête pour calculer la moyenne

- **Fonction `getTotalMovies()`**
  - Retourne le nombre total de films dans le catalogue
  - `SELECT COUNT(*) as total FROM Movie`

- **Fonction `getMostFavoritedMovie()`**
  - Trouve le film le plus ajouté aux favoris
  - Joint Movie et Favorite, groupe par film, ordonne par nombre de favoris

- **Fonction `getMostPopularCategory()`**
  - Trouve la catégorie avec le plus de films favoris
  - Joint Category, Movie et Favorite pour compter les favoris par catégorie

#### server/controller.php
- **Fonction `readStatisticsController()`**
  - Appelle toutes les fonctions statistiques
  - Retourne un objet avec toutes les métriques

#### server/script.php
- **Route `readstatistics`**
  - Endpoint : `/server/script.php?todo=readstatistics`
  - Retourne JSON avec toutes les statistiques

### 2. Frontend (JavaScript/HTML/CSS)

#### app/data/dataStatistics.js
- **Module `DataStatistics`**
  - Méthode `read()` : Appel API pour récupérer les statistiques

#### app/component/Statistics/
- **script.js**
  - Composant `Statistics` avec méthode `format()`
  - Remplace les placeholders dans le template avec les données réelles
  
- **template.html**
  - Structure HTML avec 5 cartes statistiques
  - Utilise des icônes emoji et placeholders pour les valeurs
  
- **style.css**
  - Design moderne avec cartes en grid
  - Icônes colorées avec gradient
  - Responsive design pour mobile
  - Animations au survol

#### app/index.html
- **Imports** : DataStatistics et Statistics
- **Fonction `C.handlerShowStatistics()`** : Handler pour afficher les statistiques
- **Fonction `V.renderStatistics()`** : Charge et affiche les statistiques
- **Navbar** : Bouton "Statistiques" ajouté entre "Films" et "About"

#### app/component/NavBar/
- **template.html** : Ajout du bouton "Statistiques"
- **script.js** : Paramètre `hStatistics` ajouté à la fonction `format()`

## Critères d'acceptation - Vérifié ✓

### Statistiques utilisateur & engagement
- ✓ **Nombre total de profils créés** : Affiché avec icône 👥
- ✓ **Nombre moyen de films par profil dans les favoris** : Calculé et affiché avec icône ⭐

### Statistiques films & catalogue
- ✓ **Nombre total de films dans la base** : Affiché avec icône 🎬
- ✓ **Film le plus ajouté aux favoris** : Titre et nombre de favoris avec icône 🏆
- ✓ **Catégorie la plus populaire** : Nom de catégorie et nombre de favoris avec icône 📊

## Interface utilisateur

### Design
- **Header** : Titre principal et description
- **Grid responsive** : 5 cartes statistiques s'adaptant à la taille d'écran
- **Cartes individuelles** :
  - Icône colorée avec gradient
  - Titre de la statistique
  - Valeur principale en gros
  - Description complémentaire

### Responsive
- **Desktop** : Grid avec colonnes automatiques (min 300px)
- **Mobile** : Colonnes uniques, cartes centrées

## Exemple de données

```json
{
  "total_profiles": 5,
  "average_favorites_per_profile": 2,
  "total_movies": 13,
  "most_favorited_movie": {
    "title": "La Liste de Schindler",
    "favorites_count": 2
  },
  "most_popular_category": {
    "category_name": "Drame",
    "favorites_count": 2
  }
}
```

## Respect des contraintes architecturales

- ✓ Séparation 3-tier (DB → Controller → Frontend)
- ✓ PDO prepared statements (sécurité SQL)
- ✓ Modularité des composants
- ✓ Cohérence de style avec le reste du site
- ✓ Interface responsive et accessible
- ✓ Gestion d'erreurs appropriée

## Navigation

- **Accès** : Bouton "Statistiques" dans la navbar
- **URL** : Accessible directement via la navbar (pas de changement d'URL)
- **Retour** : Bouton "Films" pour revenir à la liste des films

Cette implémentation fournit une vue d'ensemble complète et engageante des statistiques de la plateforme, encourageant l'engagement des utilisateurs tout en offrant des insights précieux sur l'utilisation du catalogue.