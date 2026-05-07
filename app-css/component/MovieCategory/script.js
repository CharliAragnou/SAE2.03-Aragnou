import Movie from "../Movie/script.js";

let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let MovieCategory = {};

MovieCategory.format = function(categoryData, favoriteIds = []) {
  if (!categoryData || !categoryData.name) {
    return "";
  }

  // Format the movies in this category with favorite info
  let moviesHtml = Movie.format(categoryData.movies, favoriteIds);

  // Replace placeholders
  let html = template;
  html = html.replaceAll("{{categoryName}}", categoryData.name);
  html = html.replaceAll("{{movies}}", moviesHtml);
  return html;
};

export default MovieCategory;
