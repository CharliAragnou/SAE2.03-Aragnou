// URL du serveur à partir du module dataProfile.js
let SERVER_SCRIPT_URL = "..";
let DataProfile = {};

DataProfile.add = async function(formData) {
    let config = {
        method: "POST",
        body: formData
    };
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=addprofile", config);
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

DataProfile.read = async function() {
    let answer = await fetch(SERVER_SCRIPT_URL + "?todo=readprofiles");
    if (!answer.ok) {
        throw new Error(`Erreur HTTP ${answer.status}`);
    }
    let data = await answer.json();
    return data;
};

export default DataProfile;