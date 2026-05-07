let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let MovieSearch = {};

MovieSearch.format = function () {
  return template;
};

export default MovieSearch;