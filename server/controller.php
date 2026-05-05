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
    $movies = getAllMovies($age);
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

    // Validate min_age
    $min_age = isset($_POST['min_age']) ? (int)$_POST['min_age'] : 0;
    if ($min_age < 0) {
        return ['success' => false, 'message' => 'Restriction d\'âge invalide'];
    }

    // Prepare data
    $profileData = [
        'name' => trim($_POST['name']),
        'avatar' => isset($_POST['avatar']) ? trim($_POST['avatar']) : null,
        'min_age' => $min_age
    ];

    // Call model
    $result = addProfile($profileData);
    if ($result) {
        return ['success' => true, 'message' => 'Le profil a été ajouté avec succès.'];
    } else {
        return ['success' => false, 'message' => 'Erreur lors de l\'ajout du profil.'];
    }
}

function readProfilesController(){
    $profiles = getAllProfiles();
    return $profiles;
}