/** global: FormData */

/**
 * GLSR.ajax and GLSR.request.data of 8.x: always posts to admin-ajax.
 */

const body = (formOrData) => {
    let formData = new FormData();
    const objectType = Object.prototype.toString.call(formOrData);
    if ('[object FormData]' === objectType) {
        formData = formOrData;
    }
    if ('[object HTMLFormElement]' === objectType) {
        formData = new FormData(formOrData);
    }
    if ('[object Object]' === objectType) {
        Object.keys(formOrData).forEach(key => formData.append(key, formOrData[key]));
    }
    formData.append('action', window.GLSR.config.request.ajax.action);
    formData.append('_ajax_request', true);
    return formData;
}

const data = (action, values = {}) => {
    const prefixed = {};
    values._action = action;
    for (let key of Object.keys(values)) {
        prefixed[`${window.GLSR.config.nameprefix}[${key}]`] = values[key];
    }
    return prefixed;
}

const get = (url, callback, headers) => {
    fetch(url, { headers: Object.assign({}, headers, { 'X-Requested-With': 'XMLHttpRequest' }) })
        .then(response => response.text())
        .then(text => callback(text))
        .catch(e => callback(e.message))
}

const post = (formOrData, callback, headers) => {
    fetch(window.GLSR.config.request.ajax.url, {
        body: body(formOrData),
        headers: Object.assign({}, headers, { 'X-Requested-With': 'XMLHttpRequest' }),
        method: 'POST',
    })
        .then(response => response.json())
        .then(json => callback(json.data, json.success))
        .catch(e => callback({ message: e.message }, false))
}

export default { data, get, post }
