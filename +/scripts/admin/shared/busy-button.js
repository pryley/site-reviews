/**
 * A button's busy state: aria-busy, the is-busy class, the data-loading text in place of its own,
 * and the buttons beside it disabled while it works.
 */
export default (el, { disable = true } = {}) => {
    const siblings = () => [...(el.parentElement?.children || [])]
        .filter(sibling => sibling !== el && 'BUTTON' === sibling.tagName && !sibling.hasAttribute('data-ajax-cancel'));
    const loaded = () => {
        if ('true' !== el.getAttribute('aria-busy')) return;
        siblings().forEach(sibling => (sibling.disabled = false))
        el.textContent = el.dataset.text;
        delete el.dataset.text;
        el.setAttribute('aria-busy', 'false');
        el.classList.remove('is-busy');
        if (disable) {
            el.disabled = false;
        }
    };
    const loading = () => {
        if ('true' === el.getAttribute('aria-busy')) return;
        siblings().forEach(sibling => (sibling.disabled = true))
        el.classList.add('is-busy');
        el.setAttribute('aria-busy', 'true');
        el.dataset.text = el.textContent;
        el.textContent = el.dataset.loading || el.dataset.text;
        el.blur();
        if (disable) {
            el.disabled = true;
        }
    };
    return { el, loaded, loading };
}
