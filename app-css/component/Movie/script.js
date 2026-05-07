let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let Movie = {};

Movie.format = function (moviesArray, favoriteIds = []) {
  // Critère d'acceptation : Message si aucun film
  if (!moviesArray || moviesArray.length === 0) {
    return `<div class="font-sans" style="padding: 2rem; text-align: center;">
              <p>Aucun film disponible pour le moment.</p>
            </div>`;
  }

  // Formatage de la liste
  let html = '<div class="movie-grid">';
  
  for (let movie of moviesArray) {
    let movieHtml = template;
    const isFav = favoriteIds.includes(movie.id);
    movieHtml = movieHtml.replaceAll("{{id}}", movie.id);
    movieHtml = movieHtml.replaceAll("{{title}}", movie.title);
    movieHtml = movieHtml.replaceAll("{{poster}}", movie.poster);
    movieHtml = movieHtml.replaceAll("{{favoriteClass}}", isFav ? "is-favorite" : "not-favorite");
    movieHtml = movieHtml.replaceAll("{{favoriteIcon}}", isFav ? "★" : "☆");
    html += movieHtml;
  }
  
  html += '</div>';
  return html;
};

export default Movie;