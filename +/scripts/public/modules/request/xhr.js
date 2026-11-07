/** global: DOMException, XMLHttpRequest */

const aborted = () => new DOMException('The request was aborted.', 'AbortError');

// The fallback admin-ajax requests do not use window.fetch
export default (url, { body, headers = {}, method = 'POST', signal } = {}) => new Promise((resolve, reject) => {
    if (signal?.aborted) {
        reject(aborted())
        return;
    }
    const request = new XMLHttpRequest();
    request.open(method, url);
    Object.entries({ ...headers, 'X-Requested-With': 'XMLHttpRequest' }).forEach(([name, value]) => request.setRequestHeader(name, value))
    request.onabort = () => reject(aborted());
    request.onerror = () => reject(new Error('network error'));
    request.onload = () => resolve({ ok: request.status >= 200 && request.status < 300, status: request.status, text: request.responseText });
    signal?.addEventListener('abort', () => request.abort())
    request.send(body);
})
