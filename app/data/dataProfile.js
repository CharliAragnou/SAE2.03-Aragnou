// URL du serveur à partir du module dataProfile.js.
// On calcule d'abord depuis le module, puis on utilise un fallback basé sur la page si nécessaire.
let SERVER_SCRIPT_URL = new URL("../../server/script.php", import.meta.url).href;
if (typeof window !== 'undefined' && SERVER_SCRIPT_URL.startsWith('file:')) {
    const path = window.location.pathname.replace(/\/app\/.*$/, '/server/script.php');
    SERVER_SCRIPT_URL = window.location.origin + path;
}

let DataProfile = {};

DataProfile.read = async function() {
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readprofiles");
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

export { DataProfile };