/** global: File, GLSR, XMLHttpRequest */

let xhr;

const get = (url, callback, headers) => {
    _prepareRequest(callback);
    xhr.open('GET', url, true);
    xhr.responseType = 'text';
    _setHeaders(headers);
    xhr.send();
}

const post = (formOrData, callback, headers) => {
    _prepareRequest(callback);
    xhr.open('POST', GLSR.ajaxurl, true);
    xhr.responseType = 'json';
    xhr.json = true; // @IE11
    _setHeaders(headers);
    xhr.send(_normalize(formOrData));
}

const _handleError = function (callback) {
    if ('json' === this.responseType || true === this.json) { // @IE11
        return callback({ message: this.statusText }, false);
    }
    else if (this.responseType === 'text') {
        return callback(this.statusText);
    }
    console.error(this);
}

const _handleSuccess = function (callback) {
    if (this.status === 0 || this.status >= 200 && this.status < 300 || this.status === 304) {
        if ('json' === this.responseType) {
            return callback(this.response.data, this.response.success);
        }
        if ('text' === this.responseType) {
            return callback(this.responseText);
        }
        if (true === this.json) { //@IE11
            const response = JSON.parse(this.response);
            return callback(response.data, response.success);
        }
        console.info(this);
    }
    else {
        this._handleError(callback);
    }
}

const _normalize = (data) => {
    let formData = new FormData();
    const objectType = Object.prototype.toString.call(data);
    if ('[object FormData]' === objectType) {
        formData = data;
    }
    if ('[object HTMLFormElement]' === objectType) {
        formData = new FormData(data);
    }
    if ('[object Object]' === objectType) {
        Object.keys(data).forEach(key => formData.append(key, data[key]));
    }
    formData.append('action', GLSR.action);
    formData.append('_ajax_request', true);
    return formData;
}

const _prepareRequest = (callback) => {
    xhr = new XMLHttpRequest();
    xhr.addEventListener('load', _handleSuccess.bind(xhr, callback))
    xhr.addEventListener('error', _handleError.bind(xhr, callback))
}

const _setHeaders = (headers) => {
    headers = headers || {};
    headers['X-Requested-With'] = 'XMLHttpRequest';
    for (let key in headers) {
        if (!headers.hasOwnProperty(key)) continue;
        xhr.setRequestHeader(key, headers[key]);
    }
}

export default { get, post }
