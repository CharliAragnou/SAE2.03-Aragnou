let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let ProfileForm = {};

ProfileForm.format = function () {
  return template;
};

export { ProfileForm };