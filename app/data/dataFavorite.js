// URL du serveur à partir du module dataFavorite.js
let SERVER_SCRIPT_URL = "..";
let DataFavorite = {};

DataFavorite.read = async function(profileId) {
    if (!profileId) {
        throw new Error('ID du profil manquant');
    }
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readfavorites&profileId=" + encodeURIComponent(profileId));
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

DataFavorite.add = async function(profileId, movieId) {
    if (!profileId || !movieId) {
        throw new Error('ID du profil ou du film manquant');
    }
    let config = {
        method: "POST",
        body: new URLSearchParams({
            profileId: profileId,
            movieId: movieId
        })
    };
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=addfavorite", config);
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

DataFavorite.remove = async function(profileId, movieId) {
    if (!profileId || !movieId) {
        throw new Error('ID du profil ou du film manquant');
    }
    let config = {
        method: "POST",
        body: new URLSearchParams({
            profileId: profileId,
            movieId: movieId
        })
    };
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=removefavorite", config);
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

export default DataFavorite;
