// URL du serveur à partir du module dataMovie.js
let SERVER_SCRIPT_URL = "..";

let DataMovie = {};

DataMovie.requestMovies = async function(age = 0, search = '', category = 0, year = 0){
    // fetch permet d'envoyer une requête HTTP à l'URL spécifiée.
    // On demande au serveur de renvoyer les films compatibles avec l'âge
    // et éventuellement filtrés par titre / catégorie / année.
    const params = new URLSearchParams({
        todo: 'readmovies',
        age: age,
        search: search,
        category: category,
        year: year
    });
    let answer = await fetch(SERVER_SCRIPT_URL + "?" + params.toString());
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

export default DataMovie;
