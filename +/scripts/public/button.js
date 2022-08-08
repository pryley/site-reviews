export default (el) => {
    const loadingText = el.dataset.loading;
    const text = el.innerText;
    const loaded = () => {
        el.setAttribute('aria-busy', false);
        el.removeAttribute('disabled');
        el.innerHTML = text;
    }
    const loading = () => {
        el.setAttribute('aria-busy', true);
        el.setAttribute('disabled', '');
        el.innerHTML = '<span class="glsr-loading"></span>' + loadingText || text;
    }
    return { el, loading, loaded };
}
