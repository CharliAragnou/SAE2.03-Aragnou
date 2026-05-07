let SERVER_SCRIPT_URL = window.location.origin + '/~aragnou1/SAE2.03-Aragnou/server/script.php';

const DataFeatured = {
  read: async function(age = 0) {
    try {
      const url = SERVER_SCRIPT_URL + `?todo=readfeatured&age=${age}`;
      const response = await fetch(url);
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      const data = await response.json();
      return data;
    } catch (error) {
      console.error("Erreur lors de la récupération des films mis en avant :", error);
      throw error;
    }
  }
};

export default DataFeatured;
