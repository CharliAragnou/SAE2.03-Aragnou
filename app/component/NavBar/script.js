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

NavBar.format = function (hAbout, hHome, profiles, hProfileChange, selectedProfileId) {
  let html = template;
  html = html.replace("{{hAbout}}", hAbout);
  html = html.replace("{{hHome}}", hHome);
  html = html.replace("{{hProfileChange}}", hProfileChange);

  let options = '';
  if (profiles) {
    for (const profile of profiles) {
      const selected = selectedProfileId === profile.id ? ' selected' : '';
      options += `<option value="${profile.id}"${selected}>${profile.name}</option>`;
    }
  }
  html = html.replace("{{profileOptions}}", options);
  return html;
};

export { NavBar };
