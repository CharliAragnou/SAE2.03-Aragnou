import { Movie } from "../Movie/script.js";

let template = "";
try {
  let templateFile = await fetch(new URL("./template.html", import.meta.url));
  if (!templateFile.ok) {
    throw new Error(`MovieCategory template fetch failed: ${templateFile.status}`);
  }
  template = await templateFile.text();
} catch (error) {
  console.warn(error);
  template = `<section class="movie-category font-sans"><h2 class="category-name">{{categoryName}}</h2><div class="category-movies">{{movies}}</div></section>`;
}

let MovieCategory = {};

MovieCategory.format = function(categoryData) {
  if (!categoryData || !categoryData.name) {
    return "";
  }

  // Format the movies in this category
  let moviesHtml = Movie.format(categoryData.movies);

  // Replace placeholders
  let html = template;
  html = html.replaceAll("{{categoryName}}", categoryData.name);
  html = html.replaceAll("{{movies}}", moviesHtml);
  return html;
};

export { MovieCategory };
