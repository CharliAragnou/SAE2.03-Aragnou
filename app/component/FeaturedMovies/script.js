import Movie from "../Movie/script.js";

let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

const FeaturedMovies = {
  format: function(featuredMovies, favoriteIds = []) {
    // If no featured movies, show empty message
    if (!featuredMovies || featuredMovies.length === 0) {
      return template.replace('{{content}}', '<div class="featured-empty">Aucun film mis en avant pour le moment.</div>');
    }

    // Build featured movies list
    let moviesHtml = '<div class="featured-movies-grid">';
    for (let movie of featuredMovies) {
      const movieData = {
        id: movie.id,
        title: movie.title,
        poster: movie.poster,
        year: movie.year,
        director: movie.director,
        description: movie.description,
        length: movie.length,
        min_age: movie.min_age,
        category: movie.category,
        trailer: movie.trailer
      };
      moviesHtml += Movie.format([movieData], favoriteIds);
    }
    moviesHtml += '</div>';

    return template.replace('{{content}}', moviesHtml);
  }
};

export default FeaturedMovies;
