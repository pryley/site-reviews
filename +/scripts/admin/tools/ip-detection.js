/**
 * The IP address detection tool: the custom header field shows for the Custom header, and the
 * trusted proxies for any header.
 */
export default class IpDetection {
    static init (root = document) {
        const select = root.querySelector('#proxy_http_header');
        if (!select) return;
        select.addEventListener('change', () => {
            document.getElementById('custom_http_header')?.parentElement.classList.toggle('hidden', 'custom' !== select.value)
            document.getElementById('trusted_proxies')?.parentElement.classList.toggle('hidden', '' === select.value)
        })
    }
}
