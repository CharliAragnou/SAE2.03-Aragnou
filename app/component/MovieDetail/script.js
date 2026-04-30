let template = "";
try {
  let templateFile = await fetch(new URL("./template.html", import.meta.url));
  if (!templateFile.ok) {
    throw new Error(`MovieDetail template fetch failed: ${templateFile.status}`);
  }
  template = await templateFile.text();
} catch (error) {
  console.warn(error);
  template = `<section class="movie-detail font-sans"><div class="movie-detail__hero"><img src="../server/images/{{poster}}" alt="Affiche de {{title}}" class="movie-detail__poster" /><div class="movie-detail__summary"><h1>{{title}}</h1><p><strong>Réalisateur :</strong> {{director}}</p><p><strong>Année :</strong> {{year}}</p><p><strong>Catégorie :</strong> {{category}}</p><p><strong>Restriction d'âge :</strong> {{min_age}} ans</p></div></div><div class="movie-detail__description"><h2>Synopsis</h2><p>{{description}}</p></div><div class="movie-detail__trailer"><h2>Trailer</h2><div class="movie-detail__video"><iframe src="{{trailer}}" title="Trailer de {{title}}" frameborder="0" allowfullscreen></iframe></div></div></section>`;
}

let MovieDetail = {};

MovieDetail.format = function(movie){
  if (!movie) {
    return `<div class="font-sans" style="padding:2rem; text-align:center;">Détails indisponibles.</div>`;
  }

  let html = template;
  html = html.replaceAll("{{title}}", movie.title || "");
  html = html.replaceAll("{{poster}}", movie.poster || "");
  html = html.replaceAll("{{description}}", movie.description || "");
  html = html.replaceAll("{{director}}", movie.director || "");
  html = html.replaceAll("{{year}}", movie.year || "");
  html = html.replaceAll("{{category}}", movie.category || "");
  html = html.replaceAll("{{min_age}}", movie.min_age !== null ? movie.min_age : "N/A");
  html = html.replaceAll("{{trailer}}", movie.trailer || "");
  return html;
};

export { MovieDetail };