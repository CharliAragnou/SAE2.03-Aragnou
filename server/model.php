<?php
/**
 * Ce fichier contient toutes les fonctions qui réalisent des opérations
 * sur la base de données, telles que les requêtes SQL pour insérer, 
 * mettre à jour, supprimer ou récupérer des données.
 */

/**
 * Définition des constantes de connexion à la base de données.
 *
 * HOST : Nom d'hôte du serveur de base de données, ici "localhost".
 * DBNAME : Nom de la base de données
 * DBLOGIN : Nom d'utilisateur pour se connecter à la base de données.
 * DBPWD : Mot de passe pour se connecter à la base de données.
 */
define("HOST", "localhost");
define("DBNAME", "aragnou1");
define("DBLOGIN", "aragnou1");
define("DBPWD", "aragnou1");


function getAllMovies($age = 0){
    // Connexion à la base de données
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }
    
    // Requête SQL pour récupérer les films avec leurs catégories, groupés par catégorie
        $sql = "SELECT c.id AS category_id, c.name AS category_name, " .
            "m.id, m.name AS title, m.image AS poster " .
            "FROM Category c " .
               "LEFT JOIN Movie m ON m.id_category = c.id AND (:age = 0 OR m.min_age <= :age OR m.min_age IS NULL) " .
            "ORDER BY c.id, m.id";
    
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Grouper les résultats par catégorie
    $groupedByCategory = [];
    foreach ($rows as $row) {
        $categoryId = $row->category_id;
        if (!isset($groupedByCategory[$categoryId])) {
            $groupedByCategory[$categoryId] = [
                'id' => $categoryId,
                'name' => $row->category_name,
                'movies' => []
            ];
        }
        // Ajouter le film uniquement si m.id n'est pas NULL (films de cette catégorie)
        if ($row->id !== null) {
            $groupedByCategory[$categoryId]['movies'][] = (object)[
                'id' => $row->id,
                'title' => $row->title,
                'poster' => $row->poster
            ];
        }
    }
    
    return array_values($groupedByCategory); // Retourner en array numéroté
}

function addMovie($movieData){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "INSERT INTO Movie (name, director, year, length, description, id_category, image, trailer, min_age) 
            VALUES (:name, :director, :year, :length, :description, :id_category, :image, :trailer, :min_age)";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':name', $movieData['name']);
    $stmt->bindParam(':director', $movieData['director']);
    $stmt->bindParam(':year', $movieData['year']);
    $stmt->bindParam(':length', $movieData['length']);
    $stmt->bindParam(':description', $movieData['description']);
    $stmt->bindParam(':id_category', $movieData['id_category']);
    $stmt->bindParam(':image', $movieData['image']);
    $stmt->bindParam(':trailer', $movieData['trailer']);
    $stmt->bindParam(':min_age', $movieData['min_age']);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Insert error: " . $e->getMessage());
        return false;
    }
}

function addProfile($profileData){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "INSERT INTO Profile (name, avatar, min_age) 
            VALUES (:name, :avatar, :min_age)";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':name', $profileData['name']);
    $stmt->bindParam(':avatar', $profileData['avatar']);
    $stmt->bindParam(':min_age', $profileData['min_age']);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Insert error: " . $e->getMessage());
        return false;
    }
}

function getAllProfiles(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT id, name, avatar, min_age FROM Profile ORDER BY name";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $profiles = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $profiles;
}

function getMovieById($id){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT m.id, m.name AS title, m.director, m.year, m.length, m.description, m.image AS poster, m.trailer, m.min_age, c.name AS category " .
           "FROM Movie m " .
           "LEFT JOIN Category c ON m.id_category = c.id " .
           "WHERE m.id = :id";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $movie = $stmt->fetch(PDO::FETCH_OBJ);
    return $movie ? $movie : false;
}