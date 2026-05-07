// URL du serveur à partir du module dataMovie.js
let SERVER_SCRIPT_URL = window.location.origin + '..';

let DataMovie = {};

DataMovie.add = async function(formData) {
    let config = {
        method: "POST",
        body: formData
    };
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=addmovie", config);
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

DataMovie.requestMovies = async function(search = '', category = 0, year = 0) {
    const params = new URLSearchParams({
        todo: 'readmovies',
        admin: '1',
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
};

DataMovie.updateFeaturedStatus = async function(movieId, featured) {
    let formData = new FormData();
    formData.append('movieId', movieId);
    formData.append('featured', featured ? 1 : 0);
    let config = {
        method: 'POST',
        body: formData
    };
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=updatefeatured", config);
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

export default DataMovie;