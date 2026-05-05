// URL du serveur à partir du module dataMovie.js
let SERVER_SCRIPT_URL = new URL("../../server/script.php", import.meta.url).href;

let DataMovie = {};

DataMovie.requestMovies = async function(age = 0){
    // fetch permet d'envoyer une requête HTTP à l'URL spécifiée.
    // On demande au serveur de ne renvoyer que les films compatibles avec l'âge.
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readmovies&age=" + encodeURIComponent(age));
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
}

DataMovie.requestMovieDetails = async function(movieId) {
    if (!movieId) {
        throw new Error('Identifiant de film manquant');
    }
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readmoviedetail&id=" + encodeURIComponent(movieId));
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
}

export {DataMovie};
