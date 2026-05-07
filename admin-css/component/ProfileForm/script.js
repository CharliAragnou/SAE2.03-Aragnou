let templateFile = await fetch(new URL("./template.html", import.meta.url));
let template = await templateFile.text();

let ProfileForm = {};

ProfileForm.format = function (profiles, selectedProfileId) {
  let html = template;
  let options = '';
  if (profiles && profiles.length) {
    for (const profile of profiles) {
      const selected = selectedProfileId === profile.id ? ' selected' : '';
      options += `<option value="${profile.id}"${selected}>${profile.name}</option>`;
    }
  }
  html = html.replace("{{profileOptions}}", options);
  return html;
};

export default ProfileForm;