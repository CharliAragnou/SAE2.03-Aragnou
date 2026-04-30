// URL du serveur à partir du module dataMovie.js.
// On calcule d'abord depuis le module, puis on utilise un fallback basé sur la page si nécessaire.
let SERVER_SCRIPT_URL = new URL("../../server/script.php", import.meta.url).href;
if (typeof window !== 'undefined' && SERVER_SCRIPT_URL.startsWith('file:')) {
    const path = window.location.pathname.replace(/\/admin\/.*$/, '/server/script.php');
    SERVER_SCRIPT_URL = window.location.origin + path;
}

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

export { DataMovie };