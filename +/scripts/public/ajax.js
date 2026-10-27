/** global: GLSR */

// Compatibility shim for addon and custom JS. It keeps the callback contract of the
// old XMLHttpRequest module and always posts to admin-ajax, so actions that only the
// Router knows about keep working. The plugin's own requests use request.js instead.
import { legacyData, legacyPost } from '@/public/request.js';
import xhr from '@/public/xhr.js';

const data = (action, values = {}) => legacyData(action, values);

const get = (url, callback, headers) => {
    xhr(url, { headers, method: 'GET' })
        .then(({ text }) => callback(text))
        .catch(e => callback(e.message))
}

const post = (formOrData, callback, headers) => {
    legacyPost(formOrData, headers).then(({ data, success }) => callback(data, success))
}

export default { data, get, post }
