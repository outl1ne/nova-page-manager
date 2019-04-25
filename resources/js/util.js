export function getParameterByName(name) {
  const url = window.location.href;
  name = name.replace(/[\[\]]/g, '\\$&');
  const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
  const results = regex.exec(url);
  if (!results) return void 0;
  if (!results[2]) return '';
  return decodeURIComponent(results[2].replace(/\+/g, ' '));
}
