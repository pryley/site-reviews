/** global: XMLHttpRequest */

// The fallback admin-ajax requests do not use window.fetch
export default (url, { body, headers = {}, method = 'POST' } = {}) => new Promise((resolve, reject) => {
    const request = new XMLHttpRequest();
    request.open(method, url);
    Object.entries({ ...headers, 'X-Requested-With': 'XMLHttpRequest' }).forEach(([name, value]) => request.setRequestHeader(name, value))
    request.onerror = () => reject(new Error('network error'));
    request.onload = () => resolve({ ok: request.status >= 200 && request.status < 300, status: request.status, text: request.responseText });
    request.send(body);
})
