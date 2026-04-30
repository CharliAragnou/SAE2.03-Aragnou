let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let MovieForm = {};

MovieForm.format = function () {
  return template;
};

export { MovieForm };