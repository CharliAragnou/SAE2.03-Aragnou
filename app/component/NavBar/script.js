let template = "";
try {
  let templateFile = await fetch(new URL("./template.html", import.meta.url));
  if (!templateFile.ok) {
    throw new Error(`NavBar template fetch failed: ${templateFile.status}`);
  }
  template = await templateFile.text();
} catch (error) {
  console.warn(error);
  template = `<nav class="navbar font-sans"><ul class="navbar__list"><li class="navbar__item" onclick="{{hHome}}">Films</li><li class="navbar__item" onclick="{{hAbout}}">About</li></ul></nav>`;
}

let NavBar = {};

NavBar.format = function (hAbout, hHome) {
  let html = template;
  html = html.replace("{{hAbout}}", hAbout);
  html = html.replace("{{hHome}}", hHome);
  return html;
};

export { NavBar };
