export default (el) => {
    const loaded = () => {
        if ('true' === el.getAttribute('aria-busy')) {
            el.innerHTML = el.dataset.text;
            el.setAttribute('aria-busy', false);
            el.removeAttribute('data-text');
            el.removeAttribute('disabled');
        }
    }
    const loading = () => {
        if ('false' === el.getAttribute('aria-busy')) {
            el.setAttribute('aria-busy', true);
            el.setAttribute('disabled', '');
            el.dataset.text = el.innerText;
            el.innerHTML = '<span class="glsr-loading"></span>' + (el.dataset.loading || el.dataset.text);
        }
    }
    return { el, loading, loaded };
}
