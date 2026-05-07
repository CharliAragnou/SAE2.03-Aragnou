let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let NavBar = {};

NavBar.format = function (hAbout, hHome, profiles, hProfileChange, selectedProfileId, hStatistics, hSearch, hSearchClick, searchQuery) {
  let html = template;
  html = html.replace("{{hAbout}}", hAbout);
  html = html.replace("{{hHome}}", hHome);
  html = html.replace("{{hStatistics}}", hStatistics);
  html = html.replace("{{hProfileChange}}", hProfileChange);
  html = html.replace("{{hSearch}}", hSearch);
  html = html.replace("{{hSearchClick}}", hSearchClick);
  const safeSearchQuery = (searchQuery || '').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  html = html.replace("{{searchQuery}}", safeSearchQuery);

  let selectedProfile = null;
  if (profiles && selectedProfileId) {
    selectedProfile = profiles.find(profile => profile.id === selectedProfileId) || null;
  }
  let avatarHtml = '<span class="profile-avatar-placeholder"></span>';
  if (selectedProfile && selectedProfile.avatar) {
    avatarHtml = `<img src="${selectedProfile.avatar}" alt="Avatar de ${selectedProfile.name}" class="profile-selector-avatar-img">`;
  }
  html = html.replace("{{profileAvatar}}", avatarHtml);

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

export default NavBar;
