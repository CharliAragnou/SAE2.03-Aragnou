// URL du serveur à partir du module dataProfile.js
let SERVER_SCRIPT_URL = window.location.origin + '..';

let DataProfile = {};

DataProfile.read = async function() {
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readprofiles");
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

export default DataProfile;