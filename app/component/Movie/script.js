let template = "";
try {
  let templateFile = await fetch(new URL("./template.html", import.meta.url));
  if (!templateFile.ok) {
    throw new Error(`Movie template fetch failed: ${templateFile.status}`);
  }
  template = await templateFile.text();
} catch (error) {
  console.warn(error);
  template = `<article class="movie-card font-sans" data-id="{{id}}" onclick="C.handlerDetail({{id}})">
    <img src="../server/images/{{poster}}" alt="Affiche de {{title}}" class="movie-poster">
    <div class="movie-info">
      <h3 class="movie-title">{{title}}</h3>
    </div>
  </article>`;
}

let Movie = {};

Movie.format = function (moviesArray) {
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
    movieHtml = movieHtml.replaceAll("{{id}}", movie.id);
    movieHtml = movieHtml.replaceAll("{{title}}", movie.title);
    movieHtml = movieHtml.replaceAll("{{poster}}", movie.poster);
    html += movieHtml;
  }
  
  html += '</div>';
  return html;
};

export { Movie };