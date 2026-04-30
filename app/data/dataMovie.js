// URL du serveur à partir du module dataMovie.js
let SERVER_SCRIPT_URL = new URL("../../server/script.php", import.meta.url).href;

let DataMovie = {};

DataMovie.requestMovies = async function(){
    // fetch permet d'envoyer une requête HTTP à l'URL spécifiée. 
    // L'URL est construite en concaténant HOST_URL à "/server/script.php?direction=" et la valeur de la variable dir. 
    // L'URL finale dépend de la valeur de HOST_URL et de dir.
        let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readmovies");
        if (!answer.ok) {
            throw new Error(`Erreur HTTP ${answer.status}`);
        }
    // answer est la réponse du serveur à la requête fetch.
    // On utilise ensuite la méthode json() pour extraire de cette réponse les données au format JSON.
    // Ces données (data) sont automatiquement converties en objet JavaScript.
    let data = await answer.json();
    // Enfin, on retourne ces données.
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
