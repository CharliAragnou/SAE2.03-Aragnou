<?php

/** ARCHITECTURE PHP SERVEUR  : Rôle du fichier controller.php
 * 
 *  Dans ce fichier, on va définir les fonctions de contrôle qui vont traiter les requêtes HTTP.
 *  Les requêtes HTTP sont interprétées selon la valeur du paramètre 'todo' de la requête (voir script.php)
 *  Pour chaque valeur différente, on déclarera une fonction de contrôle différente.
 * 
 *  Les fonctions de contrôle vont éventuellement lire les paramètres additionnels de la requête, 
 *  les vérifier, puis appeler les fonctions du modèle (model.php) pour effectuer les opérations
 *  nécessaires sur la base de données.
 *  
 *  Si la fonction échoue à traiter la requête, elle retourne false (mauvais paramètres, erreur de connexion à la BDD, etc.)
 *  Sinon elle retourne le résultat de l'opération (des données ou un message) à includre dans la réponse HTTP.
 */

/** Inclusion du fichier model.php
 *  Pour pouvoir utiliser les fonctions qui y sont déclarées et qui permettent
 *  de faire des opérations sur les données stockées en base de données.
 */
require("model.php");


function readMoviesController(){
    $age = isset($_REQUEST['age']) && is_numeric($_REQUEST['age']) ? (int)$_REQUEST['age'] : 0;
    $search = isset($_REQUEST['search']) ? trim($_REQUEST['search']) : '';
    $category = isset($_REQUEST['category']) && is_numeric($_REQUEST['category']) ? (int)$_REQUEST['category'] : 0;
    $year = isset($_REQUEST['year']) && is_numeric($_REQUEST['year']) ? (int)$_REQUEST['year'] : 0;
    $admin = isset($_REQUEST['admin']) && $_REQUEST['admin'] == '1' ? true : false;
    $movies = getAllMovies($age, $search, $category, $year, $admin);
    return $movies;
}

function readMovieDetailController(){
    if (!isset($_REQUEST['id']) || !is_numeric($_REQUEST['id'])) {
        return false;
    }

    $movieId = (int)$_REQUEST['id'];
    $movie = getMovieById($movieId);
    return $movie;
}

function addMovieController(){
    // Validate required fields
    $required = ['title', 'director', 'year', 'length', 'description', 'category', 'image'];
    foreach ($required as $field) {
        if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
            return ['success' => false, 'message' => "Champ obligatoire manquant : $field"];
        }
    }

    // Validate year
    $year = (int)$_POST['year'];
    if ($year < 1900 || $year > 2030) {
        return ['success' => false, 'message' => 'Année invalide'];
    }

    // Validate length
    $length = (int)$_POST['length'];
    if ($length <= 0) {
        return ['success' => false, 'message' => 'Durée invalide'];
    }

    // Validate min_age if provided
    $min_age = isset($_POST['min_age']) ? (int)$_POST['min_age'] : 0;
    if ($min_age < 0 || $min_age > 18) {
        return ['success' => false, 'message' => 'Restriction d\'âge invalide'];
    }

    // Prepare data
    $movieData = [
        'name' => trim($_POST['title']),
        'director' => trim($_POST['director']),
        'year' => $year,
        'length' => $length,
        'description' => trim($_POST['description']),
        'id_category' => (int)$_POST['category'],
        'image' => trim($_POST['image']),
        'trailer' => isset($_POST['trailer']) ? trim($_POST['trailer']) : null,
        'min_age' => $min_age
    ];

    // Call model
    $result = addMovie($movieData);
    if ($result) {
        return ['success' => true, 'message' => 'Le film a été ajouté avec succès.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout du film.'];
    }
}

function addProfileController(){
    // Validate required fields
    if (!isset($_POST['name']) || empty(trim($_POST['name']))) {
        return ['success' => false, 'message' => 'Champ obligatoire manquant : name'];
    }

    $profileId = isset($_POST['id']) && is_numeric($_POST['id']) ? (int)$_POST['id'] : null;

    // Validate min_age
    $min_age = isset($_POST['min_age']) ? (int)$_POST['min_age'] : 0;
    if ($min_age < 0) {
        return ['success' => false, 'message' => 'Restriction d\'âge invalide'];
    }

    // Prepare data
    $profileData = [
        'id' => $profileId,
        'name' => trim($_POST['name']),
        'avatar' => isset($_POST['avatar']) ? trim($_POST['avatar']) : null,
        'min_age' => $min_age
    ];

    // Call model
    $result = addProfile($profileData);
    if ($result) {
        if ($profileId) {
            return ['success' => true, 'message' => 'Le profil a été modifié avec succès.'];
        }
        return ['success' => true, 'message' => 'Le profil a été ajouté avec succès.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout du profil.'];
    }
}

function readProfilesController(){
    $profiles = getAllProfiles();
    return $profiles;
}

function readFavoritesController(){
    if (!isset($_REQUEST['profileId']) || !is_numeric($_REQUEST['profileId'])) {
        return false;
    }

    $profileId = (int)$_REQUEST['profileId'];
    $favorites = getFavoritesByProfile($profileId);
    return $favorites;
}

function addFavoriteController(){
    if (!isset($_POST['profileId']) || !is_numeric($_POST['profileId'])) {
        return ['success' => false, 'message' => 'ID du profil manquant'];
    }
    if (!isset($_POST['movieId']) || !is_numeric($_POST['movieId'])) {
        return ['success' => false, 'message' => 'ID du film manquant'];
    }

    $profileId = (int)$_POST['profileId'];
    $movieId = (int)$_POST['movieId'];

    // Check if already favorite
    if (isFavorite($profileId, $movieId)) {
        return ['success' => false, 'message' => 'Ce film est déjà dans vos favoris.'];
    }

    $result = addFavorite($profileId, $movieId);
    if ($result) {
        return ['success' => true, 'message' => 'Le film a été ajouté à vos favoris.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout aux favoris.'];
    }
}

function removeFavoriteController(){
    if (!isset($_POST['profileId']) || !is_numeric($_POST['profileId'])) {
        return ['success' => false, 'message' => 'ID du profil manquant'];
    }
    if (!isset($_POST['movieId']) || !is_numeric($_POST['movieId'])) {
        return ['success' => false, 'message' => 'ID du film manquant'];
    }

    $profileId = (int)$_POST['profileId'];
    $movieId = (int)$_POST['movieId'];

    $result = removeFavorite($profileId, $movieId);
    if ($result) {
        return ['success' => true, 'message' => 'Le film a été supprimé de vos favoris.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la suppression des favoris.'];
    }
}

function updateFeaturedController(){
    if (!isset($_POST['movieId']) || !is_numeric($_POST['movieId'])) {
        return ['success' => false, 'message' => 'ID du film manquant'];
    }
    if (!isset($_POST['featured']) || !is_numeric($_POST['featured'])) {
        return ['success' => false, 'message' => 'Statut featured manquant'];
    }

    $movieId = (int)$_POST['movieId'];
    $featured = (int)$_POST['featured'];
    $featured = $featured === 1 ? 1 : 0;

    $result = updateMovieFeatured($movieId, $featured);
    if ($result !== false) {
        return ['success' => true, 'message' => 'Le statut du film a été mis à jour avec succès.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de la mise à jour du statut.'];
    }
}

function readFeaturedController(){
    $age = isset($_REQUEST['age']) && is_numeric($_REQUEST['age']) ? (int)$_REQUEST['age'] : 0;
    $featured = getFeaturedMovies($age);
    return $featured;
}

function readStatisticsController(){
    $stats = [
        'total_profiles' => getTotalProfiles(),
        'average_favorites_per_profile' => getAverageFavoritesPerProfile(),
        'total_movies' => getTotalMovies(),
        'most_favorited_movie' => getMostFavoritedMovie(),
        'most_popular_category' => getMostPopularCategory()
    ];
    return $stats;
}