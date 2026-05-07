import Movie from "../Movie/script.js";

let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

const FeaturedMovies = {
  format: function(featuredMovies, favoriteIds = []) {
    // If no featured movies, show empty message
    if (!featuredMovies || featuredMovies.length === 0) {
      return template.replace('{{content}}', '<div class="featured-empty">Aucun film mis en avant pour le moment.</div>');
    }

    const [heroMovie, secondaryMovie, ...otherMovies] = featuredMovies;
    let moviesHtml = this.formatHeroMovie(heroMovie, secondaryMovie, favoriteIds);
    if (otherMovies.length > 0) {
      moviesHtml += Movie.format(otherMovies, favoriteIds);
    }

    return template.replace('{{content}}', moviesHtml);
  },

  formatHeroMovie: function(movie, secondaryMovie, favoriteIds = []) {
    const isFav = favoriteIds.includes(movie.id);
    const favoriteIcon = isFav ? '★' : '☆';
    const favoriteClass = isFav ? 'is-favorite' : 'not-favorite';
    return `
      <div class="featured-hero">
        <article class="featured-hero-card movie-card font-sans" data-id="${movie.id}" onclick="C.handlerDetail(${movie.id})">
          <button class="movie-favorite-btn ${favoriteClass}" data-id="${movie.id}" onclick="event.stopPropagation(); C.handlerToggleFavorite(${movie.id}, event)">${favoriteIcon}</button>
          <div class="featured-hero-media">
            <img src="../server/images/${movie.poster}" alt="Affiche de ${movie.title}" class="featured-hero-poster" />
            <div class="featured-hero-overlay">
              <div class="featured-hero-copy">
                <span class="featured-hero-label">Films mis en avant</span>
                <h3 class="featured-hero-title">${movie.title}</h3>
                <p class="featured-hero-meta">${movie.year} • ${movie.category}</p>
                <p class="featured-hero-description">${movie.description || ''}</p>
                <button class="featured-play-button" type="button">Play</button>
              </div>
            </div>
          </div>
          ${secondaryMovie ? this.formatSecondaryMovie(secondaryMovie, favoriteIds) : ''}
        </article>
      </div>
    `;
  }
  ,

  formatSecondaryMovie: function(movie, favoriteIds = []) {
    const isFav = favoriteIds.includes(movie.id);
    const favoriteIcon = isFav ? '★' : '☆';
    const favoriteClass = isFav ? 'is-favorite' : 'not-favorite';
    return `
      <div class="featured-secondary-card movie-card font-sans" data-id="${movie.id}" onclick="C.handlerDetail(${movie.id})">
        <button class="movie-favorite-btn ${favoriteClass}" data-id="${movie.id}" onclick="event.stopPropagation(); C.handlerToggleFavorite(${movie.id}, event)">${favoriteIcon}</button>
        <div class="featured-secondary-media">
          <img src="../server/images/${movie.poster}" alt="Affiche de ${movie.title}" class="featured-secondary-poster" />
        </div>
        <div class="featured-secondary-copy">
          <p class="featured-secondary-label">À découvrir</p>
          <h4 class="featured-secondary-title">${movie.title}</h4>
        </div>
      </div>
    `;
  }
};

export default FeaturedMovies;
