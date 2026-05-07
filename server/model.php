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
define("DBNAME", "SAE203");
define("DBLOGIN", "usersae203");
define("DBPWD", "charliamaykrc202*");


function getDatabaseConnection(){
    try {
        return new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }
}

function ensureFeaturedColumnExists($cnx){
    static $checked = false;
    if ($checked) {
        return true;
    }

    $sql = "SHOW COLUMNS FROM Movie LIKE 'featured'";
    $stmt = $cnx->prepare($sql);
    if (!$stmt->execute()) {
        return false;
    }
    $column = $stmt->fetch(PDO::FETCH_OBJ);
    if ($column) {
        $checked = true;
        return true;
    }

    $sql = "ALTER TABLE Movie ADD COLUMN featured tinyint(1) DEFAULT 0";
    $stmt = $cnx->prepare($sql);
    if ($stmt->execute()) {
        $checked = true;
        return true;
    }
    return false;
}

function getAllMovies($age = 0, $search = '', $category = 0, $year = 0, $admin = false){
    // Connexion à la base de données
    $cnx = getDatabaseConnection();
    if ($cnx === false) {
        return false;
    }
    ensureFeaturedColumnExists($cnx);
    
    // Requête SQL pour récupérer les films avec leurs catégories, groupés par catégorie
        $sql = "SELECT c.id AS category_id, c.name AS category_name, " .
            "m.id, m.name AS title, m.image AS poster, m.featured AS featured " .
            "FROM Category c " .
            "LEFT JOIN Movie m ON m.id_category = c.id " .
                ($admin ? "" : "AND m.featured = 0 ") .
                "AND (:age = 0 OR m.min_age <= :age OR m.min_age IS NULL) " .
                "AND (:search = '' OR m.name LIKE :searchPattern) " .
                "AND (:category = 0 OR m.id_category = :category) " .
                "AND (:year = 0 OR m.year = :year) " .
            "ORDER BY c.id, m.id";
    
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $searchPattern = '%' . $search . '%';
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
    $stmt->bindParam(':searchPattern', $searchPattern, PDO::PARAM_STR);
    $stmt->bindParam(':category', $category, PDO::PARAM_INT);
    $stmt->bindParam(':year', $year, PDO::PARAM_INT);
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
                'poster' => $row->poster,
                'featured' => $row->featured
            ];
        }
    }
    
    return array_values($groupedByCategory); // Retourner en array numéroté
}

function addMovie($movieData){
    $cnx = getDatabaseConnection();
    if ($cnx === false) {
        return false;
    }
    ensureFeaturedColumnExists($cnx);

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
    $cnx = getDatabaseConnection();
    if ($cnx === false) {
        return false;
    }
    ensureFeaturedColumnExists($cnx);

    $sql = "INSERT INTO Profile (id, name, avatar, min_age) 
            VALUES (:id, :name, :avatar, :min_age) 
            ON DUPLICATE KEY UPDATE name = VALUES(name), avatar = VALUES(avatar), min_age = VALUES(min_age)";
    $stmt = $cnx->prepare($sql);
    if (isset($profileData['id']) && $profileData['id'] > 0) {
        $stmt->bindParam(':id', $profileData['id'], PDO::PARAM_INT);
    } else {
        $null = null;
        $stmt->bindParam(':id', $null, PDO::PARAM_NULL);
    }
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

function getFavoritesByProfile($profileId){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT m.id, m.name AS title, m.year, m.length, m.description, m.director, m.image AS poster, m.trailer, m.min_age, c.name AS category " .
           "FROM Favorite f " .
           "JOIN Movie m ON f.id_movie = m.id " .
           "LEFT JOIN Category c ON m.id_category = c.id " .
           "WHERE f.id_profile = :profileId " .
           "ORDER BY m.name";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $stmt->execute();
    $favorites = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $favorites;
}

function isFavorite($profileId, $movieId){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT COUNT(*) as count FROM Favorite WHERE id_profile = :profileId AND id_movie = :movieId";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $stmt->bindParam(':movieId', $movieId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->count > 0;
}

function addFavorite($profileId, $movieId){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "INSERT INTO Favorite (id_profile, id_movie) VALUES (:profileId, :movieId)";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $stmt->bindParam(':movieId', $movieId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Insert favorite error: " . $e->getMessage());
        return false;
    }
}

function removeFavorite($profileId, $movieId){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "DELETE FROM Favorite WHERE id_profile = :profileId AND id_movie = :movieId";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':profileId', $profileId, PDO::PARAM_INT);
    $stmt->bindParam(':movieId', $movieId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        return true;
    } catch (PDOException $e) {
        error_log("Delete favorite error: " . $e->getMessage());
        return false;
    }
}

function getFeaturedMovies($age = 0){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT m.id, m.name AS title, m.year, m.length, m.description, m.director, m.image AS poster, m.trailer, m.min_age, c.name AS category " .
           "FROM Movie m " .
           "LEFT JOIN Category c ON m.id_category = c.id " .
           "WHERE m.featured = 1 AND (:age = 0 OR m.min_age <= :age OR m.min_age IS NULL) " .
           "ORDER BY m.name";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':age', $age, PDO::PARAM_INT);
    $stmt->execute();
    $featured = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $featured;
}

function updateMovieFeatured($movieId, $featured){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "UPDATE Movie SET featured = :featured WHERE id = :movieId";
    $stmt = $cnx->prepare($sql);
    $stmt->bindParam(':featured', $featured, PDO::PARAM_INT);
    $stmt->bindParam(':movieId', $movieId, PDO::PARAM_INT);

    try {
        $stmt->execute();
        return $stmt->rowCount() >= 0;
    } catch (PDOException $e) {
        error_log("Update featured error: " . $e->getMessage());
        return false;
    }
}

function getTotalProfiles(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT COUNT(*) as total FROM Profile";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->total;
}

function getAverageFavoritesPerProfile(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT AVG(favorites_count) as average FROM (
        SELECT COUNT(*) as favorites_count FROM Favorite GROUP BY id_profile
    ) as profile_favorites";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return round($result->average, 2);
}

function getTotalMovies(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT COUNT(*) as total FROM Movie";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result->total;
}

function getMostFavoritedMovie(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT m.name AS title, COUNT(f.id_movie) as favorites_count
            FROM Movie m
            LEFT JOIN Favorite f ON m.id = f.id_movie
            GROUP BY m.id, m.name
            ORDER BY favorites_count DESC
            LIMIT 1";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result;
}

function getMostPopularCategory(){
    try {
        $cnx = new PDO("mysql:host=".HOST.";dbname=".DBNAME, DBLOGIN, DBPWD);
    } catch (PDOException $e) {
        error_log("DB connection error: " . $e->getMessage());
        return false;
    }

    $sql = "SELECT c.name AS category_name, COUNT(f.id_movie) as favorites_count
            FROM Category c
            LEFT JOIN Movie m ON c.id = m.id_category
            LEFT JOIN Favorite f ON m.id = f.id_movie
            GROUP BY c.id, c.name
            ORDER BY favorites_count DESC
            LIMIT 1";
    $stmt = $cnx->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result;
}