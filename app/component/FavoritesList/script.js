let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let FavoritesList = {};

FavoritesList.format = function (favorites) {
  if (!favorites || favorites.length === 0) {
    return template.replace("{{favoritesList}}", 
      '<div class="favorites-empty">Vous n\'avez pas encore ajouté de favoris.</div>'
    );
  }

  let html = '';
  for (let movie of favorites) {
    html += `<div class="favorite-item">
      <button class="favorite-item-remove" data-id="${movie.id}" onclick="event.stopPropagation(); C.handlerRemoveFavorite(${movie.id}, event)">✕</button>
      <img src="../server/images/${movie.poster}" alt="Affiche de ${movie.title}" class="favorite-item-image">
      <div class="favorite-item-info">
        <h3 class="favorite-item-title">${movie.title}</h3>
      </div>
    </div>`;
  }

  return template.replace("{{favoritesList}}", html);
};

export default FavoritesList;
