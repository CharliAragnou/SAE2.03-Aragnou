let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

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

export default MovieDetail;