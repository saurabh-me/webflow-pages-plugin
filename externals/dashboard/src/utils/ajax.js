/**
 * Performs an Ajax Call
 *
 * @param {*} options
 * 
 * @returns a fetch call
 */
export function ajaxCall(options = {}) {
  const {
    action,
    nonce,
    nonceField,
    data,
    method,
    credentials,
    url
  } = options;

  if (!action) {
    throw new TypeError('Cannot make ajax call without action');
  }
  if (!nonce) {
    throw new TypeError('Cannot make ajax call without nonce');
  }
  
  if (data && typeof data !== 'object') {
    throw new TypeError('Data to send must be an object');
  }

  // WordPress automatically defines ajaxurl since WordPress 2.8
  if (!url && !window.ajaxurl) {
    throw new TypeError('Cannot make ajax call without url');
  }

  let formData = new FormData();
  formData.append('action', action);
  formData.append(nonceField || 'security', nonce);

  if (data) {
    // appends data as form encoded
    for (let key in data) {
      formData.append(key, data[key]);
    }
  }

  return fetch(url || window.ajaxurl, {
    method: method || 'POST',
    body: formData,
    credentials: credentials || 'same-origin'
  });
}
