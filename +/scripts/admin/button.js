export default (el) => {
    const loaded = () => {
        if ('true' === el.attr('aria-busy')) {
            siblings.prop('disabled', false);
            el.text(el.data('text'))
               .data('text', '')
               .attr('aria-busy', false)
               .prop('disabled', false)
               .removeClass('is-busy');
        }
    };
    const loading = () => {
        if (!!~['false', void(0)].indexOf(el.attr('aria-busy'))) {
            siblings.prop('disabled', true);
            el.addClass('is-busy')
               .prop('disabled', true)
               .attr('aria-busy', true)
               .data('text', el.text())
               .text(el.data('loading') || el.data('text'));
        }
    };
    const siblings = el.siblings(el.prop('tagName'));
    return { el, loading, loaded };
}
